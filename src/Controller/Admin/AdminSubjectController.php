<?php

namespace App\Controller\Admin;

use App\Entity\Subject;
use App\Repository\AcademicYearRepository;
use App\Repository\ChapterRepository;
use App\Repository\ClasseRepository;
use App\Repository\SubjectRepository;
use App\Repository\TermRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminSubjectController extends AbstractController
{
    #[Route('/subjects', name: 'subjects_index')]
    public function subjectsIndex(
        Request $request,
        SubjectRepository $subjectRepository,
        TermRepository $termRepository,
        AcademicYearRepository $academicYearRepository,
        ClasseRepository $classeRepository,
        EntityManagerInterface $em
    ): Response {
        $termId = $request->query->get('term_id');
        $academicYearId = $request->query->get('academic_year_id');
        $classId = $request->query->get('class_id');
        $search = trim($request->query->get('search', ''));

        $qb = $em->createQueryBuilder()
            ->select('s')
            ->from(Subject::class, 's')
            ->leftJoin('s.term', 't')
            ->orderBy('s.id', 'DESC');

        if ($termId) {
            $qb->andWhere('s.term = :termId')->setParameter('termId', $termId);
        }

        if ($academicYearId) {
            $qb->andWhere('t.academicYear = :ayId')->setParameter('ayId', $academicYearId);
        }

        if ($classId) {
            $qb->andWhere('s.id IN (SELECT IDENTITY(ta.subject) FROM App\Entity\TeacherAssignment ta WHERE ta.classe = :classId)')
               ->setParameter('classId', $classId);
        }

        if ($search !== '') {
            $qb->andWhere('s.name LIKE :search OR s.subject_code LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Filter terms dropdown by academic year if one is selected
        $termsCriteria = [];
        if ($academicYearId) {
            $termsCriteria['academicYear'] = $academicYearId;
        }

        return $this->render('admin/subjects/index.html.twig', [
            'subjects' => $qb->getQuery()->getResult(),
            'terms' => $termRepository->findBy($termsCriteria, ['id' => 'DESC']),
            'academic_years' => $academicYearRepository->findBy([], ['id' => 'DESC']),
            'classes' => $classeRepository->findBy([], ['name' => 'ASC']),
            'current_term' => $termId,
            'current_academic_year' => $academicYearId,
            'current_class' => $classId,
            'current_search' => $search,
        ]);
    }

    #[Route('/subjects/new', name: 'subjects_new', methods: ['GET', 'POST'])]
    public function subjectsNew(
        Request $request,
        SubjectRepository $subjectRepository,
        TermRepository $termRepository,
        EntityManagerInterface $em,
    ): Response {
        $errors = [];

        if ($request->isMethod('POST')) {
            $subjectCode = strtoupper(trim($request->request->get('subject_code', '')));
            $name = trim($request->request->get('name', ''));
            $gradeLevel = trim($request->request->get('grade_level', ''));
            $description = trim($request->request->get('description', ''));
            $termId = $request->request->get('term_id');
            $isActive = (bool) $request->request->get('is_active', false);

            if ($subjectCode === '') {
                $errors[] = 'Subject code is required.';
            } elseif ($subjectRepository->findOneBy(['subject_code' => $subjectCode])) {
                $errors[] = "Subject code «{$subjectCode}» is already in use.";
            }
            if ($name === '') {
                $errors[] = 'Subject name is required.';
            }

            if (empty($errors)) {
                $subject = new Subject();
                $subject->setSubjectCode($subjectCode);
                $subject->setName($name);
                $subject->setGradeLevel($gradeLevel !== '' ? $gradeLevel : null);
                $subject->setDescription($description !== '' ? $description : null);
                if ($termId) {
                    $subject->setTerm($termRepository->find($termId));
                }
                $subject->setIsActive($isActive);

                $em->persist($subject);
                $em->flush();

                $this->addFlash('success', "Subject «{$name}» created successfully.");
                $returnTo = trim((string) $request->request->get('return_to', $request->query->get('return_to', '')));
                if (str_starts_with($returnTo, '/admin/')) {
                    return $this->redirect($this->appendQueryParams($returnTo, ['subject_id' => $subject->getId()]));
                }

                return $this->redirectToRoute('admin_subjects_index');
            }
        }

        return $this->render('admin/subjects/new.html.twig', [
            'errors' => $errors,
            'terms' => $termRepository->findAll(),
        ]);
    }

    #[Route('/subjects/{id}/edit', name: 'subjects_edit', methods: ['GET', 'POST'])]
    public function subjectsEdit(
        int $id,
        Request $request,
        SubjectRepository $subjectRepository,
        TermRepository $termRepository,
        EntityManagerInterface $em,
    ): Response {
        $subject = $subjectRepository->find($id);
        if (!$subject) {
            throw $this->createNotFoundException("Subject #{$id} not found.");
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $subjectCode = strtoupper(trim($request->request->get('subject_code', '')));
            $name = trim($request->request->get('name', ''));
            $gradeLevel = trim($request->request->get('grade_level', ''));
            $description = trim($request->request->get('description', ''));
            $termId = $request->request->get('term_id');
            $isActive = (bool) $request->request->get('is_active', false);

            if ($subjectCode === '') {
                $errors[] = 'Subject code is required.';
            } else {
                $existing = $subjectRepository->findOneBy(['subject_code' => $subjectCode]);
                if ($existing && $existing->getId() !== $subject->getId()) {
                    $errors[] = "Subject code «{$subjectCode}» is already in use.";
                }
            }
            if ($name === '') {
                $errors[] = 'Subject name is required.';
            }

            if (empty($errors)) {
                $subject->setSubjectCode($subjectCode);
                $subject->setName($name);
                $subject->setGradeLevel($gradeLevel !== '' ? $gradeLevel : null);
                $subject->setDescription($description !== '' ? $description : null);
                $subject->setTerm($termId ? $termRepository->find($termId) : null);
                $subject->setIsActive($isActive);

                $em->flush();

                $this->addFlash('success', "Subject «{$name}» updated successfully.");
                return $this->redirectToRoute('admin_subjects_index');
            }
        }

        return $this->render('admin/subjects/edit.html.twig', [
            'subject' => $subject,
            'errors' => $errors,
            'terms' => $termRepository->findAll(),
        ]);
    }

    #[Route('/subjects/{id}/delete', name: 'subjects_delete', methods: ['POST'])]
    public function subjectsDelete(
        int $id,
        Request $request,
        SubjectRepository $subjectRepository,
        ChapterRepository $chapterRepository,
        EntityManagerInterface $em,
    ): Response {
        $subject = $subjectRepository->find($id);
        if (!$subject) {
            throw $this->createNotFoundException("Subject #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_subject_' . $id, $request->request->get('_token'))) {
            $inUse = $chapterRepository->findOneBy(['subject' => $subject]) !== null;
            if ($inUse) {
                $this->addFlash('error', "Subject «{$subject->getName()}» is used in chapters and cannot be deleted.");
                return $this->redirectToRoute('admin_subjects_index');
            }

            $name = $subject->getName();
            $em->remove($subject);
            $em->flush();
            $this->addFlash('success', "Subject «{$name}» deleted.");
        }

        return $this->redirectToRoute('admin_subjects_index');
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
