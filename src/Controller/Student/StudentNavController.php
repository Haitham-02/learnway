<?php

namespace App\Controller\Student;

use App\Repository\StudentEnrollmentRepository;
use App\Repository\TeacherAssignmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student', name: 'student_')]
#[IsGranted('ROLE_STUDENT')]
class StudentNavController extends AbstractController
{
    public function sidebarNav(
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        $enrollment = $enrollmentRepo->findOneBy(['user' => $this->getUser()], ['id' => 'DESC']);
        $classe = $enrollment?->getClasse();
        $assignments = $classe ? $taRepo->findBy(['classe' => $classe]) : [];

        return $this->render('student/_sidebar_nav.html.twig', [
            'classe' => $classe,
            'assignments' => $assignments,
        ]);
    }
}
