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
        EntityManagerInterface $em,
    ): Response {
        $errors = [];

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $gradeLevel = trim($request->request->get('grade_level', ''));
            $section = trim($request->request->get('section', ''));
            $isActive = (bool) $request->request->get('is_active', false);

            if ($name === '') {
                $errors[] = 'Class name is required.';
            }
            if ($gradeLevel === '') {
                $errors[] = 'Grade level is required.';
            }

            if (empty($errors)) {
                $classe = new Classe();
                $classe->setName($name);
                $classe->setGradeLevel($gradeLevel);
                $classe->setSection($section !== '' ? $section : null);
                $classe->setIsActive($isActive);
                $classe->setCreatedAt(new \DateTime());

                $em->persist($classe);
                $em->flush();

                $this->addFlash('success', "Class «{$name}» created successfully.");
                $returnTo = trim((string) $request->request->get('return_to', $request->query->get('return_to', '')));
                if (str_starts_with($returnTo, '/admin/')) {
                    return $this->redirect($this->appendQueryParams($returnTo, ['class_id' => $classe->getId()]));
                }

                return $this->redirectToRoute('admin_classes_index');
            }
        }

        return $this->render('admin/classes/new.html.twig', [
            'errors' => $errors,
        ]);
    }

    #[Route('/classes/{id}/edit', name: 'classes_edit', methods: ['GET', 'POST'])]
    public function classesEdit(
        int $id,
        Request $request,
        ClasseRepository $classeRepository,
        EntityManagerInterface $em,
    ): Response {
        $classe = $classeRepository->find($id);
        if (!$classe) {
            throw $this->createNotFoundException("Class #{$id} not found.");
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $gradeLevel = trim($request->request->get('grade_level', ''));
            $section = trim($request->request->get('section', ''));
            $isActive = (bool) $request->request->get('is_active', false);

            if ($name === '') {
                $errors[] = 'Class name is required.';
            }
            if ($gradeLevel === '') {
                $errors[] = 'Grade level is required.';
            }

            if (empty($errors)) {
                $classe->setName($name);
                $classe->setGradeLevel($gradeLevel);
                $classe->setSection($section !== '' ? $section : null);
                $classe->setIsActive($isActive);

                $em->flush();

                $this->addFlash('success', "Class «{$name}» updated successfully.");
                return $this->redirectToRoute('admin_classes_index');
            }
        }

        return $this->render('admin/classes/edit.html.twig', [
            'class' => $classe,
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
