<?php

namespace App\Controller\Student;

use App\Entity\Submission;
use App\Entity\SubmissionFile;
use App\Repository\AssignmentRepository;
use App\Repository\SubmissionRepository;
use App\Repository\StudentEnrollmentRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student', name: 'student_')]
#[IsGranted('ROLE_STUDENT')]
class StudentAssignmentController extends AbstractController
{
    #[Route('/assignment/{assignmentId}', name: 'assignment_show')]
    public function assignmentShow(
        int $assignmentId,
        AssignmentRepository $assignmentRepo,
        SubmissionRepository $submissionRepo,
        StudentEnrollmentRepository $enrollmentRepo
    ): Response {
        $assignment = $assignmentRepo->find($assignmentId);
        if (!$assignment) throw $this->createNotFoundException();

        $enrollment = $enrollmentRepo->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        if (!$enrollment || $enrollment->getClasse() !== $assignment->getChapter()->getClasse()) {
            throw $this->createAccessDeniedException();
        }

        $submission = $submissionRepo->findOneBy([
            'assignment' => $assignment,
            'student' => $this->getUser()
        ]);

        return $this->render('student/assignment_show.html.twig', [
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    #[Route('/assignment/{assignmentId}/submit', name: 'assignment_submit', methods: ['POST'])]
    public function assignmentSubmit(
        int $assignmentId,
        Request $request,
        AssignmentRepository $assignmentRepo,
        SubmissionRepository $submissionRepo,
        EntityManagerInterface $em,
        FileUploader $fileUploader
    ): Response {
        $assignment = $assignmentRepo->find($assignmentId);
        if (!$assignment) throw $this->createNotFoundException();

        // Check if student already submitted
        $submission = $submissionRepo->findOneBy([
            'assignment' => $assignment,
            'student' => $this->getUser()
        ]);

        if (!$submission) {
            $submission = new Submission();
            $submission->setAssignment($assignment);
            $submission->setStudent($this->getUser());
            $submission->setCreatedAt(new \DateTime());
        }

        $submission->setSubmittedAt(new \DateTime());
        $submission->setSubmissionText($request->request->get('submission_text'));
        $submission->setStatus('submitted');

        // Handle File Uploads
        $files = $request->files->get('files');
        if ($files) {
            foreach ($files as $file) {
                if ($file) {
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getClientMimeType();
                    $size = $file->getSize();

                    $newFilename = $fileUploader->upload($file, 'submissions/' . $assignmentId . '/' . $this->getUser()->getId());

                    $subFile = new SubmissionFile();
                    $subFile->setFileName($originalName);
                    $subFile->setFilePath('/uploads/submissions/' . $assignmentId . '/' . $this->getUser()->getId() . '/' . $newFilename);
                    $subFile->setFileType($mimeType);
                    $subFile->setFileSize($size);
                    $subFile->setUser($this->getUser());
                    $subFile->setUploadedAt(new \DateTime());
                    $subFile->setSubmission($submission);

                    $em->persist($subFile);
                }
            }
        }

        $em->persist($submission);
        $em->flush();

        $this->addFlash('success', 'Assignment submitted successfully!');
        return $this->redirectToRoute('student_assignment_show', ['assignmentId' => $assignmentId]);
    }
}
