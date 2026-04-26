<?php

namespace App\Controller\Admin;

use App\Entity\Classe;
use App\Repository\AcademicYearRepository;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminClassController extends AbstractController
{
    #[Route('/classes', name: 'classes_index')]
    public function classesIndex(ClasseRepository $classeRepository): Response
    {
        return $this->render('admin/classes/index.html.twig', [
            'classes' => $classeRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/classes/new', name: 'classes_new', methods: ['GET', 'POST'])]
    public function classesNew(
        Request $request,
        AcademicYearRepository $academicYearRepository,
        EntityManagerInterface $em,
    ): Response {
        $academicYears = $academicYearRepository->findBy([], ['id' => 'DESC']);
        $errors = [];

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $gradeLevel = trim($request->request->get('grade_level', ''));
            $section = trim($request->request->get('section', ''));
            $academicYearId = (int) $request->request->get('academic_year_id');
            $isActive = (bool) $request->request->get('is_active', false);
            $academicYear = $academicYearId ? $academicYearRepository->find($academicYearId) : null;

            if ($name === '') {
                $errors[] = 'Class name is required.';
            }
            if ($gradeLevel === '') {
                $errors[] = 'Grade level is required.';
            }
            if (!$academicYear) {
                $errors[] = 'Please select a valid academic year.';
            }

            if (empty($errors)) {
                $classe = new Classe();
                $classe->setName($name);
                $classe->setGradeLevel($gradeLevel);
                $classe->setSection($section !== '' ? $section : null);
                $classe->setAcademicYear($academicYear);
                $classe->setIsActive($isActive);
                $classe->setCreatedAt(new \DateTime());

                $em->persist($classe);
                $em->flush();

                $this->addFlash('success', "Class «{$name}» created successfully.");
                return $this->redirectToRoute('admin_classes_index');
            }
        }

        return $this->render('admin/classes/new.html.twig', [
            'academicYears' => $academicYears,
            'errors' => $errors,
        ]);
    }

    #[Route('/classes/{id}/edit', name: 'classes_edit', methods: ['GET', 'POST'])]
    public function classesEdit(
        int $id,
        Request $request,
        ClasseRepository $classeRepository,
        AcademicYearRepository $academicYearRepository,
        EntityManagerInterface $em,
    ): Response {
        $classe = $classeRepository->find($id);
        if (!$classe) {
            throw $this->createNotFoundException("Class #{$id} not found.");
        }

        $academicYears = $academicYearRepository->findBy([], ['id' => 'DESC']);
        $errors = [];

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $gradeLevel = trim($request->request->get('grade_level', ''));
            $section = trim($request->request->get('section', ''));
            $academicYearId = (int) $request->request->get('academic_year_id');
            $isActive = (bool) $request->request->get('is_active', false);
            $academicYear = $academicYearId ? $academicYearRepository->find($academicYearId) : null;

            if ($name === '') {
                $errors[] = 'Class name is required.';
            }
            if ($gradeLevel === '') {
                $errors[] = 'Grade level is required.';
            }
            if (!$academicYear) {
                $errors[] = 'Please select a valid academic year.';
            }

            if (empty($errors)) {
                $classe->setName($name);
                $classe->setGradeLevel($gradeLevel);
                $classe->setSection($section !== '' ? $section : null);
                $classe->setAcademicYear($academicYear);
                $classe->setIsActive($isActive);

                $em->flush();

                $this->addFlash('success', "Class «{$name}» updated successfully.");
                return $this->redirectToRoute('admin_classes_index');
            }
        }

        return $this->render('admin/classes/edit.html.twig', [
            'class' => $classe,
            'academicYears' => $academicYears,
            'errors' => $errors,
        ]);
    }

    #[Route('/classes/{id}/delete', name: 'classes_delete', methods: ['POST'])]
    public function classesDelete(
        int $id,
        Request $request,
        ClasseRepository $classeRepository,
        EntityManagerInterface $em,
    ): Response {
        $classe = $classeRepository->find($id);
        if (!$classe) {
            throw $this->createNotFoundException("Class #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_class_' . $id, $request->request->get('_token'))) {
            $name = $classe->getName();
            $em->remove($classe);
            $em->flush();
            $this->addFlash('success', "Class «{$name}» deleted.");
        }

        return $this->redirectToRoute('admin_classes_index');
    }
}
