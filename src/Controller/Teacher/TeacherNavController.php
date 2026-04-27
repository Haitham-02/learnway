<?php

namespace App\Controller\Teacher;

use App\Repository\TeacherAssignmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/teacher', name: 'teacher_')]
#[IsGranted('ROLE_TEACHER')]
class TeacherNavController extends AbstractTeacherController
{
    public function sidebarNav(TeacherAssignmentRepository $taRepo): Response
    {
        $assignments = $taRepo->findBy(['teacher' => $this->getUser()]);

        // Group assignments by class
        $classeMap = [];
        foreach ($assignments as $assignment) {
            $classId = $assignment->getClasse()->getId();
            if (!isset($classeMap[$classId])) {
                $classeMap[$classId] = [
                    'classe' => $assignment->getClasse(),
                    'subjects' => []
                ];
            }
            $classeMap[$classId]['subjects'][] = $assignment->getSubject();
        }

        return $this->render('teacher/_sidebar_nav.html.twig', [
            'classeMap' => $classeMap,
        ]);
    }
}
