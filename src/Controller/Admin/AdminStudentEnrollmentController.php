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
    public function studentEnrollmentsIndex(
        Request $request,
        StudentEnrollmentRepository $studentEnrollmentRepository,
        ClasseRepository $classeRepository,
    ): Response
    {
        $query = trim((string) $request->query->get('q', ''));
        $classId = (int) $request->query->get('class_id', 0);
        $active = trim((string) $request->query->get('active', ''));
        $sort = trim((string) $request->query->get('sort', 'newest'));

        return $this->render('admin/student_enrollments/index.html.twig', [
            'enrollments' => $studentEnrollmentRepository->findForAdminList(
                $query !== '' ? $query : null,
                $classId ?: null,
                $active !== '' ? $active : null,
                $sort,
            ),
            'filters' => [
                'q' => $query,
                'class_id' => $classId,
                'active' => $active,
                'sort' => $sort,
            ],
            'classesForFilter' => $classeRepository->findForSelector(),
        ]);
    }

    #[Route('/student-enrollments/bulk', name: 'student_enrollments_bulk', methods: ['POST'])]
    public function studentEnrollmentsBulk(
        Request $request,
        StudentEnrollmentRepository $studentEnrollmentRepository,
        EntityManagerInterface $em,
    ): Response {
        $selectedIds = array_values(array_filter(array_map('intval', (array) $request->request->all('selected_ids'))));
        $bulkAction = trim((string) $request->request->get('bulk_action', ''));

        if ($selectedIds === []) {
            $this->addFlash('error', 'Select at least one enrollment first.');
            return $this->redirectToRoute('admin_student_enrollments_index', $this->getFilterQuery($request));
        }

        $enrollments = $studentEnrollmentRepository->findBy(['id' => $selectedIds]);
        if ($enrollments === []) {
            $this->addFlash('error', 'Selected enrollments could not be found.');
            return $this->redirectToRoute('admin_student_enrollments_index', $this->getFilterQuery($request));
        }

        if ($bulkAction === 'delete') {
            foreach ($enrollments as $enrollment) {
                $em->remove($enrollment);
            }
            $em->flush();
            $this->addFlash('success', sprintf('%d enrollment(s) deleted.', count($enrollments)));
        } else {
            $this->addFlash('error', 'Please select a valid bulk action.');
        }

        return $this->redirectToRoute('admin_student_enrollments_index', $this->getFilterQuery($request));
    }

    #[Route('/student-enrollments/new', name: 'student_enrollments_new', methods: ['GET', 'POST'])]
    public function studentEnrollmentsNew(
        Request $request,
        StudentEnrollmentRepository $studentEnrollmentRepository,
        ClasseRepository $classeRepository,
        UserRepository $userRepository,
        \App\Repository\AcademicYearRepository $academicYearRepository,
        EntityManagerInterface $em,
    ): Response {
        $classes = $classeRepository->findForSelector();
        $users = $userRepository->findStudentsForEnrollment();
        $academicYears = $academicYearRepository->findForSelector();
        $errors = [];

        if ($request->isMethod('POST')) {
            $classId = (int) $request->request->get('class_id');
            $userId = (int) $request->request->get('user_id');
            $academicYearId = (int) $request->request->get('academic_year_id');

            $classe = $classId ? $classeRepository->find($classId) : null;
            $user = $userId ? $userRepository->findStudentForEnrollmentById($userId) : null;
            $academicYear = $academicYearId ? $academicYearRepository->find($academicYearId) : null;

            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }
            if (!$user) {
                $errors[] = 'Please select a valid student user.';
            }
            if (!$academicYear) {
                $errors[] = 'Please select a valid academic year.';
            }

            if (empty($errors)) {
                $existingEnrollment = $studentEnrollmentRepository->findOneBy([
                    'user' => $user,
                    'academicYear' => $academicYear,
                ]);

                if ($existingEnrollment) {
                    $errors[] = 'This student is already enrolled in a class for the selected academic year.';
                } else {
                    $enrollment = new StudentEnrollment();
                    $enrollment->setClasse($classe);
                    $enrollment->setUser($user);
                    $enrollment->setAcademicYear($academicYear);

                    $em->persist($enrollment);
                    $em->flush();

                    $this->addFlash('success', 'Enrollment created successfully.');
                    $returnTo = trim((string) $request->request->get('return_to', $request->query->get('return_to', '')));
                    if (str_starts_with($returnTo, '/admin/')) {
                        return $this->redirect($this->appendQueryParams($returnTo, ['enrollment_id' => $enrollment->getId()]));
                    }

                    return $this->redirectToRoute('admin_student_enrollments_index');
                }
            }
        }

        return $this->render('admin/student_enrollments/new.html.twig', [
            'classes' => $classes,
            'users' => $users,
            'academicYears' => $academicYears,
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
        \App\Repository\AcademicYearRepository $academicYearRepository,
        EntityManagerInterface $em,
    ): Response {
        $enrollment = $studentEnrollmentRepository->find($id);
        if (!$enrollment) {
            throw $this->createNotFoundException("Enrollment #{$id} not found.");
        }

        $classes = $classeRepository->findForSelector();
        $users = $userRepository->findStudentsForEnrollment();
        $academicYears = $academicYearRepository->findForSelector();
        $errors = [];

        if ($request->isMethod('POST')) {
            $classId = (int) $request->request->get('class_id');
            $userId = (int) $request->request->get('user_id');
            $academicYearId = (int) $request->request->get('academic_year_id');

            $classe = $classId ? $classeRepository->find($classId) : null;
            $user = $userId ? $userRepository->findStudentForEnrollmentById($userId) : null;
            $academicYear = $academicYearId ? $academicYearRepository->find($academicYearId) : null;

            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }
            if (!$user) {
                $errors[] = 'Please select a valid student user.';
            }
            if (!$academicYear) {
                $errors[] = 'Please select a valid academic year.';
            }

            if (empty($errors)) {
                $existingEnrollment = $studentEnrollmentRepository->findOneBy([
                    'user' => $user,
                    'academicYear' => $academicYear,
                ]);

                if ($existingEnrollment && $existingEnrollment->getId() !== $enrollment->getId()) {
                    $errors[] = 'This student is already enrolled in a class for the selected academic year.';
                } else {
                    $enrollment->setClasse($classe);
                    $enrollment->setUser($user);
                    $enrollment->setAcademicYear($academicYear);

                    $em->flush();

                    $this->addFlash('success', 'Enrollment updated successfully.');
                    return $this->redirectToRoute('admin_student_enrollments_index');
                }
            }
        }

        return $this->render('admin/student_enrollments/edit.html.twig', [
            'enrollment' => $enrollment,
            'classes' => $classes,
            'users' => $users,
            'academicYears' => $academicYears,
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

    /**
     * @param array<string, int|string|null> $params
     */
    private function appendQueryParams(string $path, array $params): string
    {
        $parts = parse_url($path);
        if (!is_array($parts)) {
            return $path;
        }

        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                $query[$key] = $value;
            }
        }

        $basePath = $parts['path'] ?? $path;
        $queryString = http_build_query($query);

        return $queryString === '' ? $basePath : $basePath . '?' . $queryString;
    }

    /**
     * @return array{q:string,class_id:int,active:string,sort:string}
     */
    private function getFilterQuery(Request $request): array
    {
        return [
            'q' => (string) $request->request->get('q', ''),
            'class_id' => (int) $request->request->get('class_id', 0),
            'active' => (string) $request->request->get('active', ''),
            'sort' => (string) $request->request->get('sort', 'newest'),
        ];
    }
}
