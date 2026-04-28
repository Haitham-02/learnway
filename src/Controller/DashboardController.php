<?php

namespace App\Controller;

use App\Repository\AnnouncementRepository;
use App\Repository\AssignmentRepository;
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
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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
        TeacherAssignmentRepository $taRepo,
        AnnouncementRepository $announcementRepo,
        AssignmentRepository $assignmentRepo,
        ChartBuilderInterface $chartBuilder
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->adminDashboard($userRepo, $classeRepo, $subjectRepo, $enrollmentRepo, $forumRepo, $chapterRepo, $chartBuilder);
        }

        if ($this->isGranted('ROLE_TEACHER')) {
            return $this->teacherDashboard($taRepo);
        }

        if ($this->isGranted('ROLE_STUDENT')) {
            return $this->studentDashboard($enrollmentRepo, $taRepo, $announcementRepo, $assignmentRepo);
        }

        // Default
        return $this->render('security/dashboard.html.twig', [
            'stats' => [],
        ]);
    }

    private function studentDashboard(
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo,
        AnnouncementRepository $announcementRepo,
        AssignmentRepository $assignmentRepo
    ): Response {
        $enrollment = $enrollmentRepo->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        
        $classe = $enrollment?->getClasse();
        $assignments = $classe ? $taRepo->findBy(['classe' => $classe]) : [];
        $announcements = $classe ? $announcementRepo->findForStudent($classe) : [];
        $upcomingAssignments = $classe ? $assignmentRepo->findUpcomingForStudent($classe, $this->getUser()) : [];

        return $this->render('student/dashboard.html.twig', [
            'enrollment' => $enrollment,
            'classe' => $classe,
            'assignments' => $assignments,
            'announcements' => $announcements,
            'upcomingAssignments' => $upcomingAssignments,
        ]);
    }

    private function adminDashboard(
        UserRepository $userRepo,
        ClasseRepository $classeRepo,
        SubjectRepository $subjectRepo,
        StudentEnrollmentRepository $enrollmentRepo,
        ForumPostRepository $forumRepo,
        ChapterRepository $chapterRepo,
        ChartBuilderInterface $chartBuilder
    ): Response {
        $stats = [
            'users' => $userRepo->count([]),
            'classes' => $classeRepo->count([]),
            'subjects' => $subjectRepo->count([]),
            'enrollments' => $enrollmentRepo->count([]),
            'chapters' => $chapterRepo->count([]),
            'forum_posts' => $forumRepo->count([]),
        ];


        // Example Chart: User roles distribution
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $chart->setData([
            'labels' => ['Students', 'Teachers', 'Admins'],
            'datasets' => [
                [
                    'backgroundColor' => ['#4f46e5', '#10b981', '#f59e0b'],
                    'data' => [
                        $userRepo->countByRole('Student'),
                        $userRepo->countByRole('Teacher'),
                        $userRepo->countByRole('Admin'),
                    ],
                ],
            ],
        ]);

        return $this->render('security/dashboard.html.twig', [
            'stats' => $stats,
            'chart' => $chart,
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
