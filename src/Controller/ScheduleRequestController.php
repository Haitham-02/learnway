<?php

namespace App\Controller;

use App\Entity\ScheduleChangeRequest;
use App\Repository\ClassScheduleRepository;
use App\Repository\TimeSlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ScheduleRequestController extends AbstractController
{
    #[Route('/schedule/request-change', name: 'app_schedule_request_change', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function requestChange(
        Request $request,
        EntityManagerInterface $em,
        ClassScheduleRepository $scheduleRepo,
        TimeSlotRepository $slotRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $scheduleId = $data['scheduleId'] ?? null;
        $proposedDay = $data['proposedDay'] ?? null;
        $proposedSlotId = $data['proposedSlot'] ?? null;
        $reason = $data['reason'] ?? null;

        if (!$scheduleId || !$proposedDay || !$proposedSlotId) {
            return new JsonResponse(['success' => false, 'error' => 'Missing required fields.'], 400);
        }

        $classSchedule = $scheduleRepo->find($scheduleId);
        if (!$classSchedule) {
            return new JsonResponse(['success' => false, 'error' => 'Schedule entry not found.'], 404);
        }

        // Security check: only the teacher of the slot can request a change
        if ($classSchedule->getTeacher() !== $this->getUser()) {
            return new JsonResponse(['success' => false, 'error' => 'Unauthorized.'], 403);
        }

        $proposedSlot = $slotRepo->find($proposedSlotId);
        if (!$proposedSlot) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid time slot.'], 400);
        }

        $changeRequest = new ScheduleChangeRequest();
        $changeRequest->setTeacher($this->getUser());
        $changeRequest->setClassSchedule($classSchedule);
        $changeRequest->setProposedTimeSlot($proposedSlot);
        $changeRequest->setProposedDayOfWeek($proposedDay);
        $changeRequest->setReason($reason);

        $em->persist($changeRequest);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
