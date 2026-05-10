<?php

namespace App\Controller;

use App\Repository\AnnouncementRepository;
use App\Repository\AssignmentRepository;
use App\Repository\ChapterRepository;
use App\Repository\ClasseRepository;
use App\Repository\ForumPostRepository;
use App\Repository\ForumCommentRepository;
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
        ForumCommentRepository $commentRepo,
        ChapterRepository $chapterRepo,
        TeacherAssignmentRepository $taRepo,
        AnnouncementRepository $announcementRepo,
        AssignmentRepository $assignmentRepo,
        ChartBuilderInterface $chartBuilder
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->adminDashboard($userRepo, $classeRepo, $subjectRepo, $enrollmentRepo, $forumRepo, $commentRepo, $chapterRepo, $chartBuilder);
        }

        if ($this->isGranted('ROLE_TEACHER')) {
            return $this->teacherDashboard($taRepo, $chartBuilder);
        }

        if ($this->isGranted('ROLE_STUDENT')) {
            return $this->studentDashboard($enrollmentRepo, $taRepo, $announcementRepo, $assignmentRepo, $chapterRepo, $chartBuilder);
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
        AssignmentRepository $assignmentRepo,
        ChapterRepository $chapterRepo,
        ChartBuilderInterface $chartBuilder
    ): Response {
        $enrollment = $enrollmentRepo->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        
        $classe = $enrollment?->getClasse();
        $assignments = $classe ? $taRepo->findBy(['classe' => $classe]) : [];
        $announcements = $classe ? $announcementRepo->findForStudent($classe) : [];
        $upcomingAssignments = $classe ? $assignmentRepo->findUpcomingForStudent($classe, $this->getUser()) : [];

        // Analytics: Assignments per Subject
        $subjectStats = [];
        foreach ($assignments as $ta) {
            $subjectName = $ta->getSubject()->getName();
            $count = $assignmentRepo->count(['chapter' => $chapterRepo->findBy(['subject' => $ta->getSubject()])]);
            // Simplified: just show count of teacher assignments for now if complex query fails
            $subjectStats[$subjectName] = count($ta->getSubject()->getChapters());
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => array_keys($subjectStats),
            'datasets' => [[
                'backgroundColor' => ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                'data' => array_values($subjectStats),
            ]],
        ]);
        $chart->setOptions([
            'plugins' => ['legend' => ['position' => 'bottom']],
            'maintainAspectRatio' => false,
        ]);

        return $this->render('student/dashboard.html.twig', [
            'enrollment' => $enrollment,
            'classe' => $classe,
            'assignments' => $assignments,
            'announcements' => $announcements,
            'upcomingAssignments' => $upcomingAssignments,
            'chart' => $chart,
        ]);
    }

    private function adminDashboard(
        UserRepository $userRepo,
        ClasseRepository $classeRepo,
        SubjectRepository $subjectRepo,
        StudentEnrollmentRepository $enrollmentRepo,
        ForumPostRepository $forumRepo,
        ForumCommentRepository $commentRepo,
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

        // Chart 1: User roles distribution
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $chart->setData([
            'labels' => ['Students', 'Teachers', 'Admins'],
            'datasets' => [[
                'backgroundColor' => ['#4f46e5', '#10b981', '#f59e0b'],
                'data' => [
                    $userRepo->countByRole('Student'),
                    $userRepo->countByRole('Teacher'),
                    $userRepo->countByRole('Admin'),
                ],
            ]],
        ]);
        $chart->setOptions(['maintainAspectRatio' => false]);

        // Chart 2: Engagement Overview
        $chart2 = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart2->setData([
            'labels' => ['Forum Posts', 'Forum Comments', 'Enrollments'],
            'datasets' => [[
                'label' => 'Total Count',
                'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                'borderColor' => '#4f46e5',
                'borderWidth' => 2,
                'data' => [
                    $forumRepo->count([]),
                    $commentRepo->count([]),
                    $enrollmentRepo->count([]),
                ],
            ]],
        ]);
        $chart2->setOptions([
            'maintainAspectRatio' => false,
            'scales' => ['y' => ['beginAtZero' => true]]
        ]);

        return $this->render('security/dashboard.html.twig', [
            'stats' => $stats,
            'chart' => $chart,
            'chart2' => $chart2,
        ]);
    }

    private function teacherDashboard(TeacherAssignmentRepository $taRepo, ChartBuilderInterface $chartBuilder): Response
    {
        $assignments = $taRepo->findByTeacher($this->getUser());

        $classeMap = [];
        $labels = [];
        $counts = [];

        foreach ($assignments as $ta) {
            $classId = $ta->getClasse()->getId();
            if (!isset($classeMap[$classId])) {
                $studentCount = $ta->getClasse()->getStudentEnrollments()->count();
                $classeMap[$classId] = [
                    'classe' => $ta->getClasse(),
                    'subjects' => [],
                    'studentCount' => $studentCount
                ];
                $labels[] = $ta->getClasse()->getName();
                $counts[] = $studentCount;
            }
            $classeMap[$classId]['subjects'][] = $ta->getSubject();
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Students per Class',
                'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                'borderColor' => '#10b981',
                'borderWidth' => 2,
                'data' => $counts,
            ]],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'scales' => ['y' => ['beginAtZero' => true]]
        ]);

        return $this->render('teacher/dashboard.html.twig', [
            'classeMap' => $classeMap,
            'assignments' => $assignments,
            'chart' => $chart,
        ]);
    }
}
