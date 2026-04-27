<?php

namespace App\Controller\Teacher;

use App\Repository\StudentEnrollmentRepository;
use App\Repository\TeacherAssignmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/teacher', name: 'teacher_')]
#[IsGranted('ROLE_TEACHER')]
class TeacherClassController extends AbstractTeacherController
{
    #[Route('/class/{classId}/students', name: 'class_students')]
    public function classStudents(
        int $classId,
        TeacherAssignmentRepository $taRepo,
        StudentEnrollmentRepository $enrollRepo
    ): Response {
        $this->denyUnlessTeacherOwnsClass($taRepo, $classId);

        // We know the teacher has at least one assignment for this class
        $assignment = $taRepo->findOneBy([
            'teacher' => $this->getUser(),
            'classe' => $classId
        ]);

        $enrollments = $enrollRepo->findBy(['classe' => $classId]);

        return $this->render('teacher/class_students.html.twig', [
            'classe' => $assignment->getClasse(),
            'enrollments' => $enrollments,
        ]);
    }
}
