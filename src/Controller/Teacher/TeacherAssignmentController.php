<?php

namespace App\Controller\Teacher;

use App\Entity\Assignment;
use App\Repository\AssignmentRepository;
use App\Repository\ChapterRepository;
use App\Repository\TeacherAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/teacher', name: 'teacher_')]
#[IsGranted('ROLE_TEACHER')]
class TeacherAssignmentController extends AbstractTeacherController
{
    // ─── ASSIGNMENTS LIST ────────────────────────────────────────

    #[Route('/subject/{subjectId}/class/{classId}/chapter/{chapterId}/assignments', name: 'assignments')]
    public function assignments(
        int $subjectId,
        int $classId,
        int $chapterId,
        TeacherAssignmentRepository $taRepo,
        ChapterRepository $chapterRepo,
    ): Response {
        $ta = $this->getTeacherAssignment($taRepo, $subjectId, $classId);
        $chapter = $chapterRepo->find($chapterId);

        if (!$chapter || $chapter->getSubject()?->getId() !== $subjectId || $chapter->getClasse()?->getId() !== $classId) {
            throw $this->createNotFoundException('Chapter not found.');
        }

        return $this->render('teacher/assignments.html.twig', [
            'ta' => $ta,
            'chapter' => $chapter,
            'assignments' => $chapter->getAssignments(),
        ]);
    }

    // ─── ASSIGNMENT CREATE ───────────────────────────────────────

    #[Route('/subject/{subjectId}/class/{classId}/chapter/{chapterId}/assignment/new', name: 'assignment_new', methods: ['GET', 'POST'])]
    public function assignmentNew(
        int $subjectId,
        int $classId,
        int $chapterId,
        Request $request,
        TeacherAssignmentRepository $taRepo,
        ChapterRepository $chapterRepo,
        EntityManagerInterface $em,
        \Symfony\Component\Mailer\MailerInterface $mailer,
        \App\Repository\StudentEnrollmentRepository $enrollRepo
    ): Response {
        $ta = $this->getTeacherAssignment($taRepo, $subjectId, $classId);
        $chapter = $chapterRepo->find($chapterId);

        if (!$chapter || $chapter->getSubject()?->getId() !== $subjectId || $chapter->getClasse()?->getId() !== $classId) {
            throw $this->createNotFoundException('Chapter not found.');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $description = trim($request->request->get('description', ''));
            $type = trim($request->request->get('type', 'homework'));
            $dueDateRaw = trim($request->request->get('due_date', ''));
            $submissionType = trim($request->request->get('submission_type', 'TEXT'));
            $status = trim($request->request->get('status', 'DRAFT'));
            $allowLate = $request->request->has('allow_late_submission');

            if ($title === '') {
                $errors[] = 'Title is required.';
            }
            if (!in_array($type, ['homework', 'quiz', 'exam'])) {
                $errors[] = 'Invalid assignment type.';
            }

            if (empty($errors)) {
                $assignment = new Assignment();
                $assignment->setChapter($chapter);
                $assignment->setTitle($title);
                $assignment->setDescription($description ?: null);
                $assignment->setType($type);
                $assignment->setSubmissionType($submissionType ?: 'TEXT');
                $assignment->setAllowLateSubmission($allowLate);
                $assignment->setStatus($status);
                $assignment->setCreatedAt(new \DateTime());
                $assignment->setUpdatedAt(new \DateTime());

                if ($dueDateRaw !== '') {
                    $dueDate = \DateTime::createFromFormat('Y-m-d\TH:i', $dueDateRaw);
                    if ($dueDate) {
                        $assignment->setDueDate($dueDate);
                    }
                }

                $em->persist($assignment);
                $em->flush();

                // Send email to students only if published
                if ($status === 'PUBLISHED') {
                    $enrollments = $enrollRepo->findBy(['classe' => $classId]);
                    foreach ($enrollments as $enrollment) {
                        $student = $enrollment->getUser();
                        if ($student->getEmail()) {
                            $email = (new \Symfony\Component\Mime\Email())
                                ->from('noreply@learnway.app')
                                ->to($student->getEmail())
                                ->subject('New Assignment: ' . $assignment->getTitle())
                                ->html('<p>Hello ' . $student->getFirstName() . ',</p><p>A new assignment has been added to the chapter "' . $chapter->getTitle() . '" in your ' . $ta->getSubject()->getName() . ' class.</p><p>Assignment: ' . $assignment->getTitle() . '</p><p>Due Date: ' . ($assignment->getDueDate() ? $assignment->getDueDate()->format('M d, Y H:i') : 'No due date') . '</p>');
                            
                            $mailer->send($email);
                        }
                    }
                    $this->addFlash('success', 'Assignment created and students notified.');
                } else {
                    $this->addFlash('success', 'Assignment created as draft (students were not notified).');
                }

                return $this->redirectToRoute('teacher_assignments', [
                    'subjectId' => $subjectId,
                    'classId' => $classId,
                    'chapterId' => $chapterId,
                ]);
            }
        }

        return $this->render('teacher/assignment_form.html.twig', [
            'ta' => $ta,
            'chapter' => $chapter,
            'assignment' => null,
            'errors' => $errors,
        ]);
    }

    // ─── ASSIGNMENT EDIT ─────────────────────────────────────────

    #[Route('/subject/{subjectId}/class/{classId}/chapter/{chapterId}/assignment/{assignmentId}/edit', name: 'assignment_edit', methods: ['GET', 'POST'])]
    public function assignmentEdit(
        int $subjectId,
        int $classId,
        int $chapterId,
        int $assignmentId,
        Request $request,
        TeacherAssignmentRepository $taRepo,
        ChapterRepository $chapterRepo,
        AssignmentRepository $assignmentRepo,
        EntityManagerInterface $em,
    ): Response {
        $ta = $this->getTeacherAssignment($taRepo, $subjectId, $classId);
        $chapter = $chapterRepo->find($chapterId);

        if (!$chapter || $chapter->getSubject()?->getId() !== $subjectId || $chapter->getClasse()?->getId() !== $classId) {
            throw $this->createNotFoundException('Chapter not found.');
        }

        $assignment = $assignmentRepo->find($assignmentId);
        if (!$assignment || $assignment->getChapter()?->getId() !== $chapterId) {
            throw $this->createNotFoundException('Assignment not found.');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $description = trim($request->request->get('description', ''));
            $type = trim($request->request->get('type', 'homework'));
            $dueDateRaw = trim($request->request->get('due_date', ''));
            $submissionType = trim($request->request->get('submission_type', 'TEXT'));
            $status = trim($request->request->get('status', 'DRAFT'));
            $allowLate = $request->request->has('allow_late_submission');

            if ($title === '') {
                $errors[] = 'Title is required.';
            }

            if (empty($errors)) {
                $assignment->setTitle($title);
                $assignment->setDescription($description ?: null);
                $assignment->setType($type);
                $assignment->setSubmissionType($submissionType ?: 'TEXT');
                $assignment->setAllowLateSubmission($allowLate);
                $assignment->setStatus($status);
                $assignment->setUpdatedAt(new \DateTime());

                if ($dueDateRaw !== '') {
                    $dueDate = \DateTime::createFromFormat('Y-m-d\TH:i', $dueDateRaw);
                    if ($dueDate) {
                        $assignment->setDueDate($dueDate);
                    }
                } else {
                    $assignment->setDueDate(null);
                }

                $em->flush();

                $this->addFlash('success', 'Assignment updated.');
                return $this->redirectToRoute('teacher_assignments', [
                    'subjectId' => $subjectId,
                    'classId' => $classId,
                    'chapterId' => $chapterId,
                ]);
            }
        }

        return $this->render('teacher/assignment_form.html.twig', [
            'ta' => $ta,
            'chapter' => $chapter,
            'assignment' => $assignment,
            'errors' => $errors,
        ]);
    }

    // ─── SUBMISSIONS ─────────────────────────────────────────────

    #[Route('/assignment/{assignmentId}/submissions', name: 'submissions')]
    public function submissions(
        int $assignmentId,
        AssignmentRepository $assignmentRepo,
        \App\Repository\SubmissionRepository $submissionRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        $assignment = $assignmentRepo->find($assignmentId);
        if (!$assignment) {
            throw $this->createNotFoundException('Assignment not found.');
        }

        // Verify the teacher owns this class
        $this->denyUnlessTeacherOwnsClass($taRepo, $assignment->getChapter()->getClasse()->getId());

        $submissions = $submissionRepo->findBy(['assignment' => $assignment]);

        return $this->render('teacher/submissions.html.twig', [
            'assignment' => $assignment,
            'submissions' => $submissions,
        ]);
    }
    #[Route('/submission/{submissionId}/review', name: 'submission_review', methods: ['GET', 'POST'])]
    public function review(
        int $submissionId,
        Request $request,
        \App\Repository\SubmissionRepository $submissionRepo,
        TeacherAssignmentRepository $taRepo,
        EntityManagerInterface $em
    ): Response {
        $submission = $submissionRepo->find($submissionId);
        if (!$submission) {
            throw $this->createNotFoundException('Submission not found.');
        }

        $assignment = $submission->getAssignment();

        // Verify the teacher owns this class
        $this->denyUnlessTeacherOwnsClass($taRepo, $assignment->getChapter()->getClasse()->getId());

        if ($request->isMethod('POST')) {
            $feedback = $request->request->get('feedback');
            $status = $request->request->get('status');

            $submission->setFeedback($feedback);
            $submission->setStatus($status);
            $submission->setReviewer($this->getUser());
            $submission->setReviewedAt(new \DateTime());

            $em->flush();

            $this->addFlash('success', 'Submission reviewed successfully.');
            return $this->redirectToRoute('teacher_submissions', ['assignmentId' => $assignment->getId()]);
        }

        return $this->render('teacher/submission_review.html.twig', [
            'submission' => $submission,
            'assignment' => $assignment,
        ]);
    }

}
