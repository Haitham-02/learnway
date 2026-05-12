<?php

namespace App\Controller;

use App\Entity\ForumPost;
use App\Entity\Livestream;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Classe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PresentationController extends AbstractController
{
    #[Route('/presentation', name: 'app_presentation')]
    public function index(EntityManagerInterface $em): Response
    {
        $userRepo = $em->getRepository(User::class);
        $forumRepo = $em->getRepository(ForumPost::class);
        $streamRepo = $em->getRepository(Livestream::class);
        $subjectRepo = $em->getRepository(Subject::class);
        $classeRepo = $em->getRepository(Classe::class);

        $stats = [
            'total_users' => $userRepo->count([]),
            'students' => $userRepo->countByRole('STUDENT'),
            'teachers' => $userRepo->countByRole('TEACHER'),
            'admins' => $userRepo->countByRole('ADMIN'),
            'forum_posts' => $forumRepo->count([]),
            'livestreams' => $streamRepo->count([]),
            'subjects' => $subjectRepo->count([]),
            'classes' => $classeRepo->count([]),
        ];

        $team = [
            [
                'name' => 'Zrafi Abdeslem',
                'role' => 'AI & CORE SYSTEMS',
                'image' => 'images/Zrafi portrait.png',
                'description' => 'Built the intelligent Copilot, the automated scheduling engine, and integrated livestreaming with advanced AI facial recognition.',
                'tech_stack' => ['Gemini AI', 'RAG', 'Qdrant', 'Jitsi', 'Symfony']
            ],
            [
                'name' => 'Besbes Yassine',
                'role' => 'COMMUNICATION & SECURITY',
                'image' => 'images/Yassine portrait.png',
                'description' => 'Developed the real-time messaging system and architected the robust authentication and role-based user management engine.',
                'tech_stack' => ['WebSockets', 'Redis', 'Security', 'HTMX']
            ],
            [
                'name' => 'Harzallah Haithem',
                'role' => 'ACADEMIC & UX ENGINEER',
                'image' => 'images/Haithem portrait.png',
                'description' => 'Created the curriculum management (subjects, lessons, assignments), the dynamic home page, the edit profile system, and the academic setup wizard.',
                'tech_stack' => ['Doctrine', 'Twig', 'UI/UX', 'CSS']
            ],
            [
                'name' => 'Zili Ali',
                'role' => 'COMMUNITY ENGINEER',
                'image' => 'images/Ali portrait.png',
                'description' => 'Designed and developed the interactive community forum, including the robust commenting, threaded discussions, and review systems.',
                'tech_stack' => ['MySQL', 'JS', 'HTMX', 'Symfony']
            ]
        ];

        return $this->render('presentation/index.html.twig', [
            'stats' => $stats,
            'team' => $team,
        ]);
    }
}
