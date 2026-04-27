<?php

namespace App\Controller;

use App\Repository\ChapterRepository;
use App\Repository\ClasseRepository;
use App\Repository\ForumPostRepository;
use App\Repository\StudentEnrollmentRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherAssignmentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(
        UserRepository $userRepo,
        ClasseRepository $classeRepo,
        SubjectRepository $subjectRepo,
        StudentEnrollmentRepository $enrollmentRepo,
        ForumPostRepository $forumRepo,
        ChapterRepository $chapterRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->adminDashboard($userRepo, $classeRepo, $subjectRepo, $enrollmentRepo, $forumRepo, $chapterRepo);
        }

        if ($this->isGranted('ROLE_TEACHER')) {
            return $this->teacherDashboard($taRepo);
        }

        if ($this->isGranted('ROLE_STUDENT')) {
            return $this->studentDashboard($enrollmentRepo, $taRepo);
        }

        // Default
        return $this->render('security/dashboard.html.twig', [
            'stats' => [],
        ]);
    }

    private function studentDashboard(
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        $enrollment = $enrollmentRepo->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        
        $classe = $enrollment?->getClasse();
        $assignments = $classe ? $taRepo->findBy(['classe' => $classe]) : [];

        return $this->render('student/dashboard.html.twig', [
            'enrollment' => $enrollment,
            'classe' => $classe,
            'assignments' => $assignments,
        ]);
    }

    private function adminDashboard(
        UserRepository $userRepo,
        ClasseRepository $classeRepo,
        SubjectRepository $subjectRepo,
        StudentEnrollmentRepository $enrollmentRepo,
        ForumPostRepository $forumRepo,
        ChapterRepository $chapterRepo
    ): Response {
        $stats = [
            'users' => $userRepo->count([]),
            'classes' => $classeRepo->count([]),
            'subjects' => $subjectRepo->count([]),
            'enrollments' => $enrollmentRepo->count([]),
            'chapters' => $chapterRepo->count([]),
            'forum_posts' => $forumRepo->count([]),
        ];

        return $this->render('security/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }

    private function teacherDashboard(TeacherAssignmentRepository $taRepo): Response
    {
        $assignments = $taRepo->findByTeacher($this->getUser());

        // Group by class
        $classeMap = [];
        foreach ($assignments as $ta) {
            $classId = $ta->getClasse()->getId();
            if (!isset($classeMap[$classId])) {
                $classeMap[$classId] = [
                    'classe' => $ta->getClasse(),
                    'subjects' => [],
                ];
            }
            $classeMap[$classId]['subjects'][] = $ta->getSubject();
        }

        return $this->render('teacher/dashboard.html.twig', [
            'classeMap' => $classeMap,
            'assignments' => $assignments,
        ]);
    }
}
