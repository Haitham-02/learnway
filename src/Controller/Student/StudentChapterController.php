<?php

namespace App\Controller\Student;

use App\Repository\ChapterRepository;
use App\Repository\StudentEnrollmentRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherAssignmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student', name: 'student_')]
#[IsGranted('ROLE_STUDENT')]
class StudentChapterController extends AbstractController
{
    #[Route('/subject/{subjectId}', name: 'subject_chapters')]
    public function chapters(
        int $subjectId,
        SubjectRepository $subjectRepo,
        ChapterRepository $chapterRepo,
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        $enrollment = $enrollmentRepo->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        if (!$enrollment) throw $this->createNotFoundException('Student enrollment not found.');
        
        $classe = $enrollment->getClasse();
        $subject = $subjectRepo->find($subjectId);
        if (!$subject) throw $this->createNotFoundException('Subject not found.');

        // Verify subject belongs to student's class
        $ta = $taRepo->findOneBy(['classe' => $classe, 'subject' => $subject]);
        if (!$ta) throw $this->createAccessDeniedException('You are not enrolled in this subject.');

        $chapters = $chapterRepo->findBy(
            ['subject' => $subject, 'classe' => $classe],
            ['sort_order' => 'ASC', 'id' => 'ASC']
        );

        return $this->render('student/chapters.html.twig', [
            'subject' => $subject,
            'chapters' => $chapters,
        ]);
    }

    #[Route('/chapter/{chapterId}', name: 'chapter_show')]
    public function chapterShow(
        int $chapterId,
        ChapterRepository $chapterRepo,
        StudentEnrollmentRepository $enrollmentRepo
    ): Response {
        $chapter = $chapterRepo->find($chapterId);
        if (!$chapter) throw $this->createNotFoundException('Chapter not found.');

        $enrollment = $enrollmentRepo->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        if (!$enrollment || $enrollment->getClasse() !== $chapter->getClasse()) {
            throw $this->createAccessDeniedException('You do not have access to this chapter.');
        }

        return $this->render('student/chapter_show.html.twig', [
            'chapter' => $chapter,
        ]);
    }
}
