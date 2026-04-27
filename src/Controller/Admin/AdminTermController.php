<?php

namespace App\Controller\Admin;

use App\Entity\Term;
use App\Repository\AcademicYearRepository;
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
class AdminTermController extends AbstractController
{
    #[Route('/terms', name: 'terms_index')]
    public function termsIndex(TermRepository $termRepository): Response
    {
        return $this->render('admin/terms/index.html.twig', [
            'terms' => $termRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/terms/new', name: 'terms_new', methods: ['GET', 'POST'])]
    public function termsNew(
        Request $request,
        AcademicYearRepository $academicYearRepository,
        TermRepository $termRepository,
        EntityManagerInterface $em,
    ): Response {
        $academicYears = $academicYearRepository->findForSelector();
        $errors = [];

        if ($request->isMethod('POST')) {
            $academicYearId = (int) $request->request->get('academic_year_id');
            $name = trim($request->request->get('name', ''));
            $startDateRaw = trim($request->request->get('start_date', ''));
            $endDateRaw = trim($request->request->get('end_date', ''));
            $isCurrent = (bool) $request->request->get('is_current', false);

            $academicYear = $academicYearId ? $academicYearRepository->find($academicYearId) : null;
            $startDate = $startDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $startDateRaw) ?: null) : null;
            $endDate = $endDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $endDateRaw) ?: null) : null;

            if (!$academicYear) {
                $errors[] = 'Please select a valid academic year.';
            }
            if ($name === '') {
                $errors[] = 'Term name is required.';
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
            if ($academicYear && $name !== '') {
                $existing = $termRepository->findOneBy([
                    'academicYear' => $academicYear,
                    'name' => $name,
                ]);
                if ($existing) {
                    $errors[] = "Term «{$name}» already exists in the selected academic year.";
                }
            }

            if (empty($errors)) {
                $term = new Term();
                $term->setAcademicYear($academicYear);
                $term->setName($name);
                $term->setStartDate($startDate);
                $term->setEndDate($endDate);
                $term->setIsCurrent($isCurrent);

                if ($isCurrent) {
                    foreach ($termRepository->findBy(['academicYear' => $academicYear, 'is_current' => true]) as $currentTerm) {
                        $currentTerm->setIsCurrent(false);
                    }
                }

                $em->persist($term);
                try {
                    $em->flush();
                } catch (UniqueConstraintViolationException) {
                    $errors[] = 'Another term in this academic year is already marked as current.';
                }

                if (empty($errors)) {
                    $this->addFlash('success', "Term «{$name}» created successfully.");
                    $returnTo = trim((string) $request->request->get('return_to', $request->query->get('return_to', '')));
                    if (str_starts_with($returnTo, '/admin/')) {
                        return $this->redirect($this->appendQueryParams($returnTo, ['term_id' => $term->getId()]));
                    }

                    return $this->redirectToRoute('admin_terms_index');
                }
            }
        }

        return $this->render('admin/terms/new.html.twig', [
            'academicYears' => $academicYears,
            'errors' => $errors,
        ]);
    }

    #[Route('/terms/{id}/edit', name: 'terms_edit', methods: ['GET', 'POST'])]
    public function termsEdit(
        int $id,
        Request $request,
        AcademicYearRepository $academicYearRepository,
        TermRepository $termRepository,
        EntityManagerInterface $em,
    ): Response {
        $term = $termRepository->find($id);
        if (!$term) {
            throw $this->createNotFoundException("Term #{$id} not found.");
        }

        $academicYears = $academicYearRepository->findForSelector();
        $errors = [];

        if ($request->isMethod('POST')) {
            $academicYearId = (int) $request->request->get('academic_year_id');
            $name = trim($request->request->get('name', ''));
            $startDateRaw = trim($request->request->get('start_date', ''));
            $endDateRaw = trim($request->request->get('end_date', ''));
            $isCurrent = (bool) $request->request->get('is_current', false);

            $academicYear = $academicYearId ? $academicYearRepository->find($academicYearId) : null;
            $startDate = $startDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $startDateRaw) ?: null) : null;
            $endDate = $endDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $endDateRaw) ?: null) : null;

            if (!$academicYear) {
                $errors[] = 'Please select a valid academic year.';
            }
            if ($name === '') {
                $errors[] = 'Term name is required.';
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
            if ($academicYear && $name !== '') {
                $existing = $termRepository->findOneBy([
                    'academicYear' => $academicYear,
                    'name' => $name,
                ]);
                if ($existing && $existing->getId() !== $term->getId()) {
                    $errors[] = "Term «{$name}» already exists in the selected academic year.";
                }
            }

            if (empty($errors)) {
                $term->setAcademicYear($academicYear);
                $term->setName($name);
                $term->setStartDate($startDate);
                $term->setEndDate($endDate);
                $term->setIsCurrent($isCurrent);

                if ($isCurrent) {
                    foreach ($termRepository->findBy(['academicYear' => $academicYear, 'is_current' => true]) as $currentTerm) {
                        if ($currentTerm->getId() !== $term->getId()) {
                            $currentTerm->setIsCurrent(false);
                        }
                    }
                }

                try {
                    $em->flush();
                } catch (UniqueConstraintViolationException) {
                    $errors[] = 'Another term in this academic year is already marked as current.';
                }

                if (empty($errors)) {
                    $this->addFlash('success', "Term «{$name}» updated successfully.");
                    return $this->redirectToRoute('admin_terms_index');
                }
            }
        }

        return $this->render('admin/terms/edit.html.twig', [
            'term' => $term,
            'academicYears' => $academicYears,
            'errors' => $errors,
        ]);
    }

    #[Route('/terms/{id}/delete', name: 'terms_delete', methods: ['POST'])]
    public function termsDelete(
        int $id,
        Request $request,
        TermRepository $termRepository,
        EntityManagerInterface $em,
    ): Response {
        $term = $termRepository->find($id);
        if (!$term) {
            throw $this->createNotFoundException("Term #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_term_' . $id, $request->request->get('_token'))) {
            $name = $term->getName();
            $em->remove($term);
            $em->flush();
            $this->addFlash('success', "Term «{$name}» deleted.");
        }

        return $this->redirectToRoute('admin_terms_index');
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
