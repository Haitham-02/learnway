<?php

namespace App\Service;

use App\Entity\ClassSchedule;
use App\Repository\ClasseRepository;
use App\Repository\ClassScheduleRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherAssignmentRepository;
use App\Repository\TimeSlotRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AutoSchedulerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClasseRepository $classeRepo,
        private TeacherAssignmentRepository $taRepo,
        private TimeSlotRepository $slotRepo,
        private ClassScheduleRepository $scheduleRepo,
        private SubjectRepository $subjectRepo,
        private UserRepository $userRepo,
        private HttpClientInterface $httpClient
    ) {}

    public function generateGlobalSchedule(): array
    {
        // 1. Fetch all required data
        $classes = $this->classeRepo->findAll();
        $assignments = $this->taRepo->findAll();
        $slots = $this->slotRepo->findBy(['type' => '90MIN']); // using 90MIN slots as standard for auto-scheduler

        $classIds = array_map(fn($c) => $c->getId(), $classes);
        $slotIds = array_map(fn($s) => $s->getId(), $slots);
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Extract unique teachers and subjects from assignments
        $teacherIds = [];
        $subjectIds = [];
        $assignmentPayload = [];

        foreach ($assignments as $a) {
            $tId = $a->getTeacher()->getId();
            $sId = $a->getSubject()->getId();
            if (!in_array($tId, $teacherIds)) $teacherIds[] = $tId;
            if (!in_array($sId, $subjectIds)) $subjectIds[] = $sId;

            $assignmentPayload[] = [
                'id' => $a->getId(),
                'class_id' => $a->getClasse()->getId(),
                'teacher_id' => $tId,
                'subject_id' => $sId,
                'frequency' => 2 // Defaulting to 2 times a week
            ];
        }

        $payload = [
            'classes' => $classIds,
            'teachers' => $teacherIds,
            'subjects' => $subjectIds,
            'days' => $days,
            'slots' => $slotIds,
            'assignments' => $assignmentPayload
        ];

        // 2. Call the Python Solver
        try {
            $response = $this->httpClient->request('POST', 'http://127.0.0.1:8001/solve', [
                'json' => $payload,
                'timeout' => 30
            ]);

            $result = $response->toArray();
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Solver Error: ' . $e->getMessage()];
        }

        if (!isset($result['success']) || !$result['success']) {
            return ['success' => false, 'error' => 'Solver failed to find a valid schedule.'];
        }

        // 3. Clear existing schedule
        $existingSchedules = $this->scheduleRepo->findAll();
        foreach ($existingSchedules as $es) {
            $this->entityManager->remove($es);
        }
        $this->entityManager->flush();

        // 4. Save new schedule
        $savedCount = 0;
        foreach ($result['schedule'] as $item) {
            $schedule = new ClassSchedule();
            $schedule->setClasse($this->classeRepo->find($item['class_id']));
            $schedule->setTeacher($this->userRepo->find($item['teacher_id']));
            $schedule->setSubject($this->subjectRepo->find($item['subject_id']));
            $schedule->setTimeSlot($this->slotRepo->find($item['slot_id']));
            $schedule->setDayOfWeek($item['day']);
            
            $this->entityManager->persist($schedule);
            $savedCount++;
        }
        
        $this->entityManager->flush();

        return ['success' => true, 'count' => $savedCount];
    }
}
