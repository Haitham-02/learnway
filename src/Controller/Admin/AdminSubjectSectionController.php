<?php

namespace App\Controller\Admin;

use App\Entity\SubjectSection;
use App\Repository\ClasseRepository;
use App\Repository\SubjectRepository;
use App\Repository\SubjectSectionRepository;
use App\Repository\TermRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminSubjectSectionController extends AbstractController
{
    #[Route('/subject-sections', name: 'subject_sections_index')]
    public function subjectSectionsIndex(SubjectSectionRepository $subjectSectionRepository): Response
    {
        return $this->render('admin/subject_sections/index.html.twig', [
            'sections' => $subjectSectionRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/subject-sections/new', name: 'subject_sections_new', methods: ['GET', 'POST'])]
    public function subjectSectionsNew(
        Request $request,
        SubjectSectionRepository $subjectSectionRepository,
        ClasseRepository $classeRepository,
        SubjectRepository $subjectRepository,
        TermRepository $termRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): Response {
        $classes = $classeRepository->findBy([], ['name' => 'ASC']);
        $subjects = $subjectRepository->findBy([], ['name' => 'ASC']);
        $terms = $termRepository->findBy([], ['id' => 'DESC']);
        $users = $userRepository->findTeachers();
        $statuses = ['PLANNED', 'OPEN', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'];
        $errors = [];

        if ($request->isMethod('POST')) {
            $classId = (int) $request->request->get('class_id');
            $subjectId = (int) $request->request->get('subject_id');
            $termId = (int) $request->request->get('term_id');
            $teacherId = (int) $request->request->get('teacher_id');
            $roomNumber = trim($request->request->get('room_number', ''));
            $status = trim($request->request->get('status', 'PLANNED'));
            $assignedAtRaw = trim($request->request->get('assigned_at', ''));
            $endedAtRaw = trim($request->request->get('ended_at', ''));

            $classe = $classId ? $classeRepository->find($classId) : null;
            $subject = $subjectId ? $subjectRepository->find($subjectId) : null;
            $term = $termId ? $termRepository->find($termId) : null;
            $teacher = $teacherId ? $userRepository->findTeacherById($teacherId) : null;
            $assignedAt = $assignedAtRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $assignedAtRaw) ?: null) : null;
            $endedAt = $endedAtRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $endedAtRaw) ?: null) : null;

            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }
            if (!$subject) {
                $errors[] = 'Please select a valid subject.';
            }
            if (!$term) {
                $errors[] = 'Please select a valid term.';
            }
            if ($teacherId && !$teacher) {
                $errors[] = 'Please select a valid teacher user.';
            }
            if (!in_array($status, $statuses, true)) {
                $errors[] = 'Please select a valid status.';
            }
            if ($assignedAtRaw !== '' && !$assignedAt) {
                $errors[] = 'Assigned date must be a valid date.';
            }
            if ($endedAtRaw !== '' && !$endedAt) {
                $errors[] = 'Ended date must be a valid date.';
            }
            if ($assignedAt && $endedAt && $endedAt < $assignedAt) {
                $errors[] = 'Ended date cannot be earlier than assigned date.';
            }
            if ($classe && $subject && $term) {
                $existing = $subjectSectionRepository->findOneBy([
                    'classe' => $classe,
                    'subject' => $subject,
                    'term' => $term,
                ]);
                if ($existing) {
                    $errors[] = 'This class/subject/term combination already exists.';
                }
            }

            if (empty($errors)) {
                $section = new SubjectSection();
                $section->setClasse($classe);
                $section->setSubject($subject);
                $section->setTerm($term);
                $section->setUser($teacher);
                $section->setRoomNumber($roomNumber !== '' ? $roomNumber : null);
                $section->setStatus($status);
                $section->setCreatedAt(new \DateTime());
                $section->setAssignedAt($assignedAt);
                $section->setEndedAt($endedAt);

                $em->persist($section);
                $em->flush();

                $this->addFlash('success', 'Subject section created successfully.');
                return $this->redirectToRoute('admin_subject_sections_index');
            }
        }

        return $this->render('admin/subject_sections/new.html.twig', [
            'classes' => $classes,
            'subjects' => $subjects,
            'terms' => $terms,
            'users' => $users,
            'statuses' => $statuses,
            'errors' => $errors,
        ]);
    }

    #[Route('/subject-sections/{id}/edit', name: 'subject_sections_edit', methods: ['GET', 'POST'])]
    public function subjectSectionsEdit(
        int $id,
        Request $request,
        SubjectSectionRepository $subjectSectionRepository,
        ClasseRepository $classeRepository,
        SubjectRepository $subjectRepository,
        TermRepository $termRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): Response {
        $section = $subjectSectionRepository->find($id);
        if (!$section) {
            throw $this->createNotFoundException("Subject section #{$id} not found.");
        }

        $classes = $classeRepository->findBy([], ['name' => 'ASC']);
        $subjects = $subjectRepository->findBy([], ['name' => 'ASC']);
        $terms = $termRepository->findBy([], ['id' => 'DESC']);
        $users = $userRepository->findTeachers();
        $statuses = ['PLANNED', 'OPEN', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'];
        $errors = [];

        if ($request->isMethod('POST')) {
            $classId = (int) $request->request->get('class_id');
            $subjectId = (int) $request->request->get('subject_id');
            $termId = (int) $request->request->get('term_id');
            $teacherId = (int) $request->request->get('teacher_id');
            $roomNumber = trim($request->request->get('room_number', ''));
            $status = trim($request->request->get('status', 'PLANNED'));
            $assignedAtRaw = trim($request->request->get('assigned_at', ''));
            $endedAtRaw = trim($request->request->get('ended_at', ''));

            $classe = $classId ? $classeRepository->find($classId) : null;
            $subject = $subjectId ? $subjectRepository->find($subjectId) : null;
            $term = $termId ? $termRepository->find($termId) : null;
            $teacher = $teacherId ? $userRepository->findTeacherById($teacherId) : null;
            $assignedAt = $assignedAtRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $assignedAtRaw) ?: null) : null;
            $endedAt = $endedAtRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $endedAtRaw) ?: null) : null;

            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }
            if (!$subject) {
                $errors[] = 'Please select a valid subject.';
            }
            if (!$term) {
                $errors[] = 'Please select a valid term.';
            }
            if ($teacherId && !$teacher) {
                $errors[] = 'Please select a valid teacher user.';
            }
            if (!in_array($status, $statuses, true)) {
                $errors[] = 'Please select a valid status.';
            }
            if ($assignedAtRaw !== '' && !$assignedAt) {
                $errors[] = 'Assigned date must be a valid date.';
            }
            if ($endedAtRaw !== '' && !$endedAt) {
                $errors[] = 'Ended date must be a valid date.';
            }
            if ($assignedAt && $endedAt && $endedAt < $assignedAt) {
                $errors[] = 'Ended date cannot be earlier than assigned date.';
            }
            if ($classe && $subject && $term) {
                $existing = $subjectSectionRepository->findOneBy([
                    'classe' => $classe,
                    'subject' => $subject,
                    'term' => $term,
                ]);
                if ($existing && $existing->getId() !== $section->getId()) {
                    $errors[] = 'This class/subject/term combination already exists.';
                }
            }

            if (empty($errors)) {
                $section->setClasse($classe);
                $section->setSubject($subject);
                $section->setTerm($term);
                $section->setUser($teacher);
                $section->setRoomNumber($roomNumber !== '' ? $roomNumber : null);
                $section->setStatus($status);
                $section->setAssignedAt($assignedAt);
                $section->setEndedAt($endedAt);

                $em->flush();

                $this->addFlash('success', 'Subject section updated successfully.');
                return $this->redirectToRoute('admin_subject_sections_index');
            }
        }

        return $this->render('admin/subject_sections/edit.html.twig', [
            'section' => $section,
            'classes' => $classes,
            'subjects' => $subjects,
            'terms' => $terms,
            'users' => $users,
            'statuses' => $statuses,
            'errors' => $errors,
        ]);
    }

    #[Route('/subject-sections/{id}/delete', name: 'subject_sections_delete', methods: ['POST'])]
    public function subjectSectionsDelete(
        int $id,
        Request $request,
        SubjectSectionRepository $subjectSectionRepository,
        EntityManagerInterface $em,
    ): Response {
        $section = $subjectSectionRepository->find($id);
        if (!$section) {
            throw $this->createNotFoundException("Subject section #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_subject_section_' . $id, $request->request->get('_token'))) {
            $em->remove($section);
            $em->flush();
            $this->addFlash('success', 'Subject section deleted.');
        }

        return $this->redirectToRoute('admin_subject_sections_index');
    }
}
