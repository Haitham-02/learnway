<?php

namespace App\Controller\Admin;

use App\Entity\AcademicYear;
use App\Repository\AcademicYearRepository;
use App\Repository\ClasseRepository;
use App\Repository\TermRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminAcademicYearController extends AbstractController
{
    #[Route('/academic-years', name: 'academic_years_index')]
    public function academicYearsIndex(AcademicYearRepository $academicYearRepository): Response
    {
        return $this->render('admin/academic_years/index.html.twig', [
            'academicYears' => $academicYearRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/academic-years/new', name: 'academic_years_new', methods: ['GET', 'POST'])]
    public function academicYearsNew(
        Request $request,
        AcademicYearRepository $academicYearRepository,
        EntityManagerInterface $em,
    ): Response {
        $errors = [];

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $startDateRaw = trim($request->request->get('start_date', ''));
            $endDateRaw = trim($request->request->get('end_date', ''));
            $isCurrent = (bool) $request->request->get('is_current', false);

            $startDate = $startDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $startDateRaw) ?: null) : null;
            $endDate = $endDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $endDateRaw) ?: null) : null;

            if ($name === '') {
                $errors[] = 'Academic year name is required.';
            } elseif ($academicYearRepository->findOneBy(['name' => $name])) {
                $errors[] = "Academic year «{$name}» already exists.";
            }
            if (!$startDate) {
                $errors[] = 'Start date is required and must be valid.';
            }
            if (!$endDate) {
                $errors[] = 'End date is required and must be valid.';
            }
            if ($startDate && $endDate && $endDate < $startDate) {
                $errors[] = 'End date cannot be earlier than start date.';
            }

            if (empty($errors)) {
                $academicYear = new AcademicYear();
                $academicYear->setName($name);
                $academicYear->setStartDate($startDate);
                $academicYear->setEndDate($endDate);
                $academicYear->setIsCurrent($isCurrent);

                if ($isCurrent) {
                    foreach ($academicYearRepository->findBy(['is_current' => true]) as $currentYear) {
                        $currentYear->setIsCurrent(false);
                    }
                }

                $em->persist($academicYear);
                try {
                    $em->flush();
                } catch (UniqueConstraintViolationException) {
                    $errors[] = 'Another academic year is already marked as current.';
                }

                if (empty($errors)) {
                    $this->addFlash('success', "Academic year «{$name}» created successfully.");
                    $returnTo = trim((string) $request->request->get('return_to', $request->query->get('return_to', '')));
                    if (str_starts_with($returnTo, '/admin/')) {
                        return $this->redirect($this->appendQueryParams($returnTo, ['academic_year_id' => $academicYear->getId()]));
                    }

                    return $this->redirectToRoute('admin_academic_years_index');
                }
            }
        }

        return $this->render('admin/academic_years/new.html.twig', [
            'errors' => $errors,
        ]);
    }

    #[Route('/academic-years/{id}/edit', name: 'academic_years_edit', methods: ['GET', 'POST'])]
    public function academicYearsEdit(
        int $id,
        Request $request,
        AcademicYearRepository $academicYearRepository,
        EntityManagerInterface $em,
    ): Response {
        $academicYear = $academicYearRepository->find($id);
        if (!$academicYear) {
            throw $this->createNotFoundException("Academic year #{$id} not found.");
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $startDateRaw = trim($request->request->get('start_date', ''));
            $endDateRaw = trim($request->request->get('end_date', ''));
            $isCurrent = (bool) $request->request->get('is_current', false);

            $startDate = $startDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $startDateRaw) ?: null) : null;
            $endDate = $endDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $endDateRaw) ?: null) : null;

            if ($name === '') {
                $errors[] = 'Academic year name is required.';
            } else {
                $existing = $academicYearRepository->findOneBy(['name' => $name]);
                if ($existing && $existing->getId() !== $academicYear->getId()) {
                    $errors[] = "Academic year «{$name}» already exists.";
                }
            }
            if (!$startDate) {
                $errors[] = 'Start date is required and must be valid.';
            }
            if (!$endDate) {
                $errors[] = 'End date is required and must be valid.';
            }
            if ($startDate && $endDate && $endDate < $startDate) {
                $errors[] = 'End date cannot be earlier than start date.';
            }

            if (empty($errors)) {
                $academicYear->setName($name);
                $academicYear->setStartDate($startDate);
                $academicYear->setEndDate($endDate);
                $academicYear->setIsCurrent($isCurrent);

                if ($isCurrent) {
                    foreach ($academicYearRepository->findBy(['is_current' => true]) as $currentYear) {
                        if ($currentYear->getId() !== $academicYear->getId()) {
                            $currentYear->setIsCurrent(false);
                        }
                    }
                }

                try {
                    $em->flush();
                } catch (UniqueConstraintViolationException) {
                    $errors[] = 'Another academic year is already marked as current.';
                }

                if (empty($errors)) {
                    $this->addFlash('success', "Academic year «{$name}» updated successfully.");
                    return $this->redirectToRoute('admin_academic_years_index');
                }
            }
        }

        return $this->render('admin/academic_years/edit.html.twig', [
            'academicYear' => $academicYear,
            'errors' => $errors,
        ]);
    }

    #[Route('/academic-years/{id}/delete', name: 'academic_years_delete', methods: ['POST'])]
    public function academicYearsDelete(
        int $id,
        Request $request,
        AcademicYearRepository $academicYearRepository,
        ClasseRepository $classeRepository,
        TermRepository $termRepository,
        EntityManagerInterface $em,
    ): Response {
        $academicYear = $academicYearRepository->find($id);
        if (!$academicYear) {
            throw $this->createNotFoundException("Academic year #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_academic_year_' . $id, $request->request->get('_token'))) {
            $hasEnrollments = $em->getRepository(\App\Entity\StudentEnrollment::class)->findOneBy(['academicYear' => $academicYear]) !== null;
            $hasTerms = $termRepository->findOneBy(['academicYear' => $academicYear]) !== null;

            if ($hasEnrollments || $hasTerms) {
                $this->addFlash('error', "Academic year «{$academicYear->getName()}» is in use by student enrollments or terms and cannot be deleted.");
                return $this->redirectToRoute('admin_academic_years_index');
            }

            $name = $academicYear->getName();
            $em->remove($academicYear);
            $em->flush();
            $this->addFlash('success', "Academic year «{$name}» deleted.");
        }

        return $this->redirectToRoute('admin_academic_years_index');
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
}
