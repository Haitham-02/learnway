<?php

namespace App\Controller\Admin;

use App\Entity\TeacherAssignment;
use App\Repository\ClasseRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherAssignmentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminTeacherAssignmentController extends AbstractController
{
    #[Route('/teacher-assignments', name: 'teacher_assignments_index')]
    public function index(TeacherAssignmentRepository $repo): Response
    {
        return $this->render('admin/teacher_assignments/index.html.twig', [
            'assignments' => $repo->findAll(),
        ]);
    }

    #[Route('/teacher-assignments/new', name: 'teacher_assignments_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserRepository $userRepository,
        SubjectRepository $subjectRepository,
        ClasseRepository $classeRepository,
        TeacherAssignmentRepository $taRepo,
        EntityManagerInterface $em,
    ): Response {
        $teachers = $userRepository->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->where('r.name = :role')
            ->setParameter('role', 'TEACHER')
            ->orderBy('u.last_name', 'ASC')
            ->getQuery()
            ->getResult();

        $subjects = $subjectRepository->findBy([], ['name' => 'ASC']);
        $classes = $classeRepository->findForSelector();
        $errors = [];

        if ($request->isMethod('POST')) {
            $teacherId = (int) $request->request->get('teacher_id');
            $subjectId = (int) $request->request->get('subject_id');
            $classId = (int) $request->request->get('class_id');

            $teacher = $teacherId ? $userRepository->find($teacherId) : null;
            $subject = $subjectId ? $subjectRepository->find($subjectId) : null;
            $classe = $classId ? $classeRepository->find($classId) : null;

            if (!$teacher) {
                $errors[] = 'Please select a valid teacher.';
            }
            if (!$subject) {
                $errors[] = 'Please select a valid subject.';
            }
            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }

            if (empty($errors)) {
                $existing = $taRepo->findOneBy([
                    'teacher' => $teacher,
                    'subject' => $subject,
                    'classe' => $classe,
                ]);

                if ($existing) {
                    $errors[] = 'This teacher is already assigned to this subject for this class.';
                } else {
                    $ta = new TeacherAssignment();
                    $ta->setTeacher($teacher);
                    $ta->setSubject($subject);
                    $ta->setClasse($classe);

                    $em->persist($ta);
                    $em->flush();

                    $this->addFlash('success', 'Teacher assignment created successfully.');
                    return $this->redirectToRoute('admin_teacher_assignments_index');
                }
            }
        }

        return $this->render('admin/teacher_assignments/new.html.twig', [
            'teachers' => $teachers,
            'subjects' => $subjects,
            'classes' => $classes,
            'errors' => $errors,
        ]);
    }

    #[Route('/teacher-assignments/{id}/delete', name: 'teacher_assignments_delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        TeacherAssignmentRepository $repo,
        EntityManagerInterface $em,
    ): Response {
        $ta = $repo->find($id);
        if (!$ta) {
            throw $this->createNotFoundException("Teacher assignment #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_teacher_assignment_' . $id, $request->request->get('_token'))) {
            $em->remove($ta);
            $em->flush();
            $this->addFlash('success', 'Teacher assignment deleted.');
        }

        return $this->redirectToRoute('admin_teacher_assignments_index');
    }
}
