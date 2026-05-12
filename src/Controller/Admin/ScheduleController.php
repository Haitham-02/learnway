<?php

namespace App\Controller\Admin;

use App\Entity\Announcement;
use App\Entity\ClassSchedule;
use App\Repository\ClasseRepository;
use App\Repository\ClassScheduleRepository;
use App\Repository\SubjectRepository;
use App\Repository\TimeSlotRepository;
use App\Repository\TeacherAssignmentRepository;
use App\Repository\UserRepository;
use App\Service\ScheduleConflictService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/schedule')]
#[IsGranted('ROLE_ADMIN')]
class ScheduleController extends AbstractController
{
    #[Route('', name: 'admin_schedule_index')]
    public function index(
        ClasseRepository $classeRepo, 
        ClassScheduleRepository $scheduleRepo,
        \App\Repository\ScheduleChangeRequestRepository $requestRepo
    ): Response {
        $classes = $classeRepo->findAll();
        $pendingRequests = $requestRepo->findBy(['status' => 'PENDING'], ['createdAt' => 'DESC']);
        
        // Efficiently find which classes have schedules
        $scheduledIds = $scheduleRepo->createQueryBuilder('s')
            ->select('DISTINCT c.id')
            ->join('s.classe', 'c')
            ->getQuery()
            ->getSingleColumnResult();

        return $this->render('admin/schedule/index.html.twig', [
            'classes' => $classes,
            'scheduledIds' => $scheduledIds,
            'pendingRequests' => $pendingRequests,
        ]);
    }

    #[Route('/wizard/{classId}', name: 'admin_schedule_wizard')]
    public function wizard(
        int $classId, 
        ClasseRepository $classeRepo, 
        TimeSlotRepository $slotRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        $classe = $classeRepo->find($classId);
        if (!$classe) throw $this->createNotFoundException();

        // Get subjects assigned to this class
        $assignments = $taRepo->findBy(['classe' => $classe]);
        
        return $this->render('admin/schedule/wizard.html.twig', [
            'classe' => $classe,
            'slots60' => $slotRepo->findBy(['type' => '60MIN']),
            'slots90' => $slotRepo->findBy(['type' => '90MIN']),
            'slots120' => $slotRepo->findBy(['type' => '120MIN']),
            'assignments' => $assignments,
            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        ]);
    }

    #[Route('/api/save-slot', name: 'admin_schedule_api_save', methods: ['POST'])]
    public function saveSlot(
        Request $request,
        EntityManagerInterface $em,
        ScheduleConflictService $conflictService,
        ClasseRepository $classeRepo,
        SubjectRepository $subjectRepo,
        UserRepository $userRepo,
        TimeSlotRepository $slotRepo,
        ClassScheduleRepository $scheduleRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $classId = $data['classId'];
        $subjectId = $data['subjectId'];
        $teacherId = $data['teacherId'];
        $slotId = $data['slotId'];
        $day = $data['day'];

        // 1. Validate
        $errors = $conflictService->validateSlot($classId, $subjectId, $teacherId, $slotId, $day);
        if (!empty($errors)) {
            return new JsonResponse(['success' => false, 'errors' => $errors], 400);
        }

        // 2. Save
        $schedule = new ClassSchedule();
        $schedule->setClasse($classeRepo->find($classId));
        $schedule->setSubject($subjectRepo->find($subjectId));
        $schedule->setTeacher($userRepo->find($teacherId));
        $schedule->setTimeSlot($slotRepo->find($slotId));
        $schedule->setDayOfWeek($day);
        
        $em->persist($schedule);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/api/delete-slot/{id}', name: 'admin_schedule_api_delete', methods: ['DELETE'])]
    public function deleteSlot(ClassSchedule $schedule, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($schedule);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/api/auto-schedule', name: 'admin_schedule_api_auto', methods: ['POST'])]
    public function autoSchedule(\App\Service\AutoSchedulerService $scheduler): JsonResponse
    {
        $result = $scheduler->generateGlobalSchedule();
        if ($result['success']) {
            return new JsonResponse(['success' => true, 'message' => 'Successfully generated ' . $result['count'] . ' schedule slots.']);
        }
        return new JsonResponse(['success' => false, 'error' => $result['error']], 400);
    }

    #[Route('/api/get-schedule/{classId}', name: 'admin_schedule_api_get')]
    public function getSchedule(int $classId, ClassScheduleRepository $scheduleRepo): JsonResponse
    {
        $schedules = $scheduleRepo->findBy(['classe' => $classId]);
        $data = [];
        foreach ($schedules as $s) {
            $data[] = [
                'id' => $s->getId(),
                'day' => $s->getDayOfWeek(),
                'slotId' => $s->getTimeSlot()->getId(),
                'subject' => $s->getSubject()->getName(),
                'teacher' => $s->getTeacher()->getFirstName() . ' ' . $s->getTeacher()->getLastName(),
            ];
        }
        return new JsonResponse($data);
    }

    #[Route('/request/approve/{id}', name: 'admin_schedule_request_approve', methods: ['POST'])]
    public function approveRequest(
        \App\Entity\ScheduleChangeRequest $changeRequest, 
        EntityManagerInterface $em,
        ScheduleConflictService $conflictService,
        \App\Repository\ScheduleChangeRequestRepository $requestRepo,
        Request $request
    ): Response {
        $classSchedule = $changeRequest->getClassSchedule();
        
        // ── CONFLICT CHECK ──
        $errors = $conflictService->validateSlot(
            $classSchedule->getClasse()->getId(),
            $classSchedule->getSubject()->getId(),
            $classSchedule->getTeacher()->getId(),
            $changeRequest->getProposedTimeSlot()->getId(),
            $changeRequest->getProposedDayOfWeek()
        );

        if (!empty($errors)) {
            return new JsonResponse(['success' => false, 'error' => implode(' ', $errors)], 400);
        }

        // Apply the change
        $classSchedule->setTimeSlot($changeRequest->getProposedTimeSlot());
        $classSchedule->setDayOfWeek($changeRequest->getProposedDayOfWeek());
        
        $changeRequest->setStatus('APPROVED');
        
        // ── AUTOMATED ANNOUNCEMENT ──
        $classe = $classSchedule->getClasse();
        $subjectName = $classSchedule->getSubject()->getName();
        $newDay = $changeRequest->getProposedDayOfWeek();
        $newTime = $changeRequest->getProposedTimeSlot()->getRange();

        $announcement = new Announcement();
        $announcement->setTitle("Schedule Update: " . $subjectName);
        $announcement->setContent(sprintf(
            "The schedule for %s has been updated. New slot: %s at %s. Please check your timetable for details.",
            $subjectName,
            $newDay,
            $newTime
        ));
        $announcement->setTargetType('CLASS');
        $announcement->setTargetId($classe->getId());
        $announcement->setUser($this->getUser());
        $announcement->setPublishAt(new \DateTime());
        $announcement->setPriority('HIGH');
        
        $em->persist($announcement);
        $em->flush();
        
        if ($request->headers->get('HX-Request')) {
            $pendingCount = count($requestRepo->findBy(['status' => 'PENDING']));
            return new Response(
                sprintf('<div id="stat-pending-count" hx-swap-oob="innerHTML">%d</div>', $pendingCount)
            );
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/request/reject/{id}', name: 'admin_schedule_request_reject', methods: ['POST'])]
    public function rejectRequest(
        \App\Entity\ScheduleChangeRequest $changeRequest, 
        EntityManagerInterface $em,
        \App\Repository\ScheduleChangeRequestRepository $requestRepo,
        Request $request
    ): Response {
        $changeRequest->setStatus('REJECTED');
        $em->flush();
        
        if ($request->headers->get('HX-Request')) {
            $pendingCount = count($requestRepo->findBy(['status' => 'PENDING']));
            return new Response(
                sprintf('<div id="stat-pending-count" hx-swap-oob="innerHTML">%d</div>', $pendingCount)
            );
        }

        return new JsonResponse(['success' => true]);
    }
}
