<?php

namespace App\Controller\Admin;

use App\Entity\Subject;
use App\Repository\SubjectRepository;
use App\Repository\SubjectSectionRepository;
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
    public function subjectsIndex(SubjectRepository $subjectRepository): Response
    {
        return $this->render('admin/subjects/index.html.twig', [
            'subjects' => $subjectRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/subjects/new', name: 'subjects_new', methods: ['GET', 'POST'])]
    public function subjectsNew(
        Request $request,
        SubjectRepository $subjectRepository,
        EntityManagerInterface $em,
    ): Response {
        $errors = [];

        if ($request->isMethod('POST')) {
            $subjectCode = strtoupper(trim($request->request->get('subject_code', '')));
            $name = trim($request->request->get('name', ''));
            $description = trim($request->request->get('description', ''));
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
                $subject->setDescription($description !== '' ? $description : null);
                $subject->setIsActive($isActive);

                $em->persist($subject);
                $em->flush();

                $this->addFlash('success', "Subject «{$name}» created successfully.");
                return $this->redirectToRoute('admin_subjects_index');
            }
        }

        return $this->render('admin/subjects/new.html.twig', [
            'errors' => $errors,
        ]);
    }

    #[Route('/subjects/{id}/edit', name: 'subjects_edit', methods: ['GET', 'POST'])]
    public function subjectsEdit(
        int $id,
        Request $request,
        SubjectRepository $subjectRepository,
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
            $description = trim($request->request->get('description', ''));
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
                $subject->setDescription($description !== '' ? $description : null);
                $subject->setIsActive($isActive);

                $em->flush();

                $this->addFlash('success', "Subject «{$name}» updated successfully.");
                return $this->redirectToRoute('admin_subjects_index');
            }
        }

        return $this->render('admin/subjects/edit.html.twig', [
            'subject' => $subject,
            'errors' => $errors,
        ]);
    }

    #[Route('/subjects/{id}/delete', name: 'subjects_delete', methods: ['POST'])]
    public function subjectsDelete(
        int $id,
        Request $request,
        SubjectRepository $subjectRepository,
        SubjectSectionRepository $subjectSectionRepository,
        EntityManagerInterface $em,
    ): Response {
        $subject = $subjectRepository->find($id);
        if (!$subject) {
            throw $this->createNotFoundException("Subject #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_subject_' . $id, $request->request->get('_token'))) {
            $inUse = $subjectSectionRepository->findOneBy(['subject' => $subject]) !== null;
            if ($inUse) {
                $this->addFlash('error', "Subject «{$subject->getName()}» is used in subject sections and cannot be deleted.");
                return $this->redirectToRoute('admin_subjects_index');
            }

            $name = $subject->getName();
            $em->remove($subject);
            $em->flush();
            $this->addFlash('success', "Subject «{$name}» deleted.");
        }

        return $this->redirectToRoute('admin_subjects_index');
    }
}
