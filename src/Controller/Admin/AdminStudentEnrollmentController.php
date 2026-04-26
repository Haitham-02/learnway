<?php

namespace App\Controller\Admin;

use App\Entity\StudentEnrollment;
use App\Repository\ClasseRepository;
use App\Repository\StudentEnrollmentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminStudentEnrollmentController extends AbstractController
{
    #[Route('/student-enrollments', name: 'student_enrollments_index')]
    public function studentEnrollmentsIndex(StudentEnrollmentRepository $studentEnrollmentRepository): Response
    {
        return $this->render('admin/student_enrollments/index.html.twig', [
            'enrollments' => $studentEnrollmentRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/student-enrollments/new', name: 'student_enrollments_new', methods: ['GET', 'POST'])]
    public function studentEnrollmentsNew(
        Request $request,
        ClasseRepository $classeRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): Response {
        $classes = $classeRepository->findBy([], ['name' => 'ASC']);
        $users = $userRepository->findStudentsForEnrollment();
        $errors = [];

        if ($request->isMethod('POST')) {
            $classId = (int) $request->request->get('class_id');
            $userId = (int) $request->request->get('user_id');
            $enrolledAtRaw = trim($request->request->get('enrolled_at', ''));
            $leftAtRaw = trim($request->request->get('left_at', ''));

            $classe = $classId ? $classeRepository->find($classId) : null;
            $user = $userId ? $userRepository->findStudentForEnrollmentById($userId) : null;
            $enrolledAt = \DateTime::createFromFormat('Y-m-d', $enrolledAtRaw) ?: null;
            $leftAt = $leftAtRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $leftAtRaw) ?: null) : null;

            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }
            if (!$user) {
                $errors[] = 'Please select a valid student user.';
            }
            if (!$enrolledAt) {
                $errors[] = 'Enrollment date is required and must be valid.';
            }
            if ($leftAtRaw !== '' && !$leftAt) {
                $errors[] = 'Left date must be a valid date.';
            }
            if ($enrolledAt && $leftAt && $leftAt < $enrolledAt) {
                $errors[] = 'Left date cannot be earlier than enrollment date.';
            }

            if (empty($errors)) {
                $enrollment = new StudentEnrollment();
                $enrollment->setClasse($classe);
                $enrollment->setUser($user);
                $enrollment->setEnrolledAt($enrolledAt);
                $enrollment->setLeftAt($leftAt);

                $em->persist($enrollment);
                $em->flush();

                $this->addFlash('success', 'Enrollment created successfully.');
                return $this->redirectToRoute('admin_student_enrollments_index');
            }
        }

        return $this->render('admin/student_enrollments/new.html.twig', [
            'classes' => $classes,
            'users' => $users,
            'errors' => $errors,
        ]);
    }

    #[Route('/student-enrollments/{id}/edit', name: 'student_enrollments_edit', methods: ['GET', 'POST'])]
    public function studentEnrollmentsEdit(
        int $id,
        Request $request,
        StudentEnrollmentRepository $studentEnrollmentRepository,
        ClasseRepository $classeRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): Response {
        $enrollment = $studentEnrollmentRepository->find($id);
        if (!$enrollment) {
            throw $this->createNotFoundException("Enrollment #{$id} not found.");
        }

        $classes = $classeRepository->findBy([], ['name' => 'ASC']);
        $users = $userRepository->findStudentsForEnrollment();
        $errors = [];

        if ($request->isMethod('POST')) {
            $classId = (int) $request->request->get('class_id');
            $userId = (int) $request->request->get('user_id');
            $enrolledAtRaw = trim($request->request->get('enrolled_at', ''));
            $leftAtRaw = trim($request->request->get('left_at', ''));

            $classe = $classId ? $classeRepository->find($classId) : null;
            $user = $userId ? $userRepository->findStudentForEnrollmentById($userId) : null;
            $enrolledAt = \DateTime::createFromFormat('Y-m-d', $enrolledAtRaw) ?: null;
            $leftAt = $leftAtRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $leftAtRaw) ?: null) : null;

            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }
            if (!$user) {
                $errors[] = 'Please select a valid student user.';
            }
            if (!$enrolledAt) {
                $errors[] = 'Enrollment date is required and must be valid.';
            }
            if ($leftAtRaw !== '' && !$leftAt) {
                $errors[] = 'Left date must be a valid date.';
            }
            if ($enrolledAt && $leftAt && $leftAt < $enrolledAt) {
                $errors[] = 'Left date cannot be earlier than enrollment date.';
            }

            if (empty($errors)) {
                $enrollment->setClasse($classe);
                $enrollment->setUser($user);
                $enrollment->setEnrolledAt($enrolledAt);
                $enrollment->setLeftAt($leftAt);

                $em->flush();

                $this->addFlash('success', 'Enrollment updated successfully.');
                return $this->redirectToRoute('admin_student_enrollments_index');
            }
        }

        return $this->render('admin/student_enrollments/edit.html.twig', [
            'enrollment' => $enrollment,
            'classes' => $classes,
            'users' => $users,
            'errors' => $errors,
        ]);
    }

    #[Route('/student-enrollments/{id}/delete', name: 'student_enrollments_delete', methods: ['POST'])]
    public function studentEnrollmentsDelete(
        int $id,
        Request $request,
        StudentEnrollmentRepository $studentEnrollmentRepository,
        EntityManagerInterface $em,
    ): Response {
        $enrollment = $studentEnrollmentRepository->find($id);
        if (!$enrollment) {
            throw $this->createNotFoundException("Enrollment #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_student_enrollment_' . $id, $request->request->get('_token'))) {
            $em->remove($enrollment);
            $em->flush();
            $this->addFlash('success', 'Enrollment deleted.');
        }

        return $this->redirectToRoute('admin_student_enrollments_index');
    }
}
