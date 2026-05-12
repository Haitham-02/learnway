<?php

namespace App\Controller;

use App\Repository\ClassScheduleRepository;
use App\Repository\StudentEnrollmentRepository;
use App\Repository\TimeSlotRepository;
use App\Service\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/schedule')]
#[IsGranted('ROLE_USER')]
class ScheduleViewController extends AbstractController
{
    #[Route('', name: 'app_schedule_view')]
    public function view(
        ClassScheduleRepository $scheduleRepo,
        StudentEnrollmentRepository $enrollRepo,
        TimeSlotRepository $slotRepo,
        \Symfony\Component\HttpFoundation\Request $request
    ): Response {
        $user = $this->getUser();
        $isTeacher = $this->isGranted('ROLE_TEACHER');
        $isStudent = $this->isGranted('ROLE_STUDENT');

        $scheduleData = [];
        $title = "My Schedule";
        $slots = [];

        if ($isStudent) {
            $enrollment = $enrollRepo->findOneBy(['user' => $user], ['id' => 'DESC']);
            if ($enrollment) {
                $class = $enrollment->getClasse();
                $title = "Class Schedule: " . $class->getName();
                $schedules = $scheduleRepo->findByClass($class->getId());
                
                // Determine slot type based on what's scheduled
                $type = !empty($schedules) ? $schedules[0]->getTimeSlot()->getType() : '90MIN';
                $slots = $slotRepo->findBy(['type' => $type]);
                
                foreach ($schedules as $s) {
                    $scheduleData[$s->getDayOfWeek()][$s->getTimeSlot()->getId()] = [
                        'subject' => $s->getSubject()->getName(),
                        'teacher' => $s->getTeacher()->getFirstName() . ' ' . $s->getTeacher()->getLastName(),
                    ];
                }
            }
        } elseif ($isTeacher) {
            $title = "Teaching Schedule";
            $schedules = $scheduleRepo->findByTeacher($user->getId());
            
            // For teachers, we might have mixed slots, but let's show all unique slots they teach in
            $slotIds = array_unique(array_map(fn($s) => $s->getTimeSlot()->getId(), $schedules));
            $slots = $slotRepo->findBy(['id' => $slotIds]);
            
            foreach ($schedules as $s) {
                $scheduleData[$s->getDayOfWeek()][$s->getTimeSlot()->getId()] = [
                    'id' => $s->getId(),
                    'subject' => $s->getSubject()->getName(),
                    'class' => $s->getClasse()->getName(),
                ];
            }
        }

        $template = $request->headers->get('HX-Request') 
            ? 'schedule/_grid.html.twig' 
            : 'schedule/view.html.twig';

        return $this->render($template, [
            'title' => $title,
            'slots' => $slots,
            'scheduleData' => $scheduleData,
            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            'isTeacher' => $isTeacher,
        ]);
    }

    #[Route('/export', name: 'app_schedule_export')]
    public function export(
        ClassScheduleRepository $scheduleRepo,
        StudentEnrollmentRepository $enrollRepo,
        TimeSlotRepository $slotRepo,
        PdfService $pdfService
    ): Response {
        $user = $this->getUser();
        $isTeacher = $this->isGranted('ROLE_TEACHER');
        
        // Similar logic to fetch data
        // ... (truncated for brevity, using same logic as view)
        
        $scheduleData = [];
        $slots = [];
        $header = "";

        if ($this->isGranted('ROLE_STUDENT')) {
            $enrollment = $enrollRepo->findOneBy(['user' => $user], ['id' => 'DESC']);
            $class = $enrollment?->getClasse();
            $header = "Class Timetable: " . ($class?->getName() ?? 'N/A');
            $schedules = $class ? $scheduleRepo->findByClass($class->getId()) : [];
            $type = !empty($schedules) ? $schedules[0]->getTimeSlot()->getType() : '90MIN';
            $slots = $slotRepo->findBy(['type' => $type]);
            foreach ($schedules as $s) {
                $scheduleData[$s->getDayOfWeek()][$s->getTimeSlot()->getId()] = [
                    'top' => $s->getSubject()->getName(),
                    'bottom' => $s->getTeacher()->getFirstName() . ' ' . $s->getTeacher()->getLastName(),
                ];
            }
        } else {
            $header = "Teacher Timetable: " . $user->getFirstName() . ' ' . $user->getLastName();
            $schedules = $scheduleRepo->findByTeacher($user->getId());
            $slotIds = array_unique(array_map(fn($s) => $s->getTimeSlot()->getId(), $schedules));
            $slots = $slotRepo->findBy(['id' => $slotIds]);
            foreach ($schedules as $s) {
                $scheduleData[$s->getDayOfWeek()][$s->getTimeSlot()->getId()] = [
                    'top' => $s->getSubject()->getName(),
                    'bottom' => $s->getClasse()->getName(),
                ];
            }
        }

        return $pdfService->generatePdfResponse('schedule/pdf.html.twig', [
            'header' => $header,
            'slots' => $slots,
            'scheduleData' => $scheduleData,
            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        ], 'Schedule_' . date('Y-m-d'));
    }
}
