<?php

namespace App\Service;

use App\Entity\ClassSchedule;
use App\Repository\ClassScheduleRepository;
use App\Repository\TeacherAssignmentRepository;

class ScheduleConflictService
{
    public function __construct(
        private ClassScheduleRepository $scheduleRepo,
        private TeacherAssignmentRepository $taRepo
    ) {}

    /**
     * Checks for any conflicts before saving a schedule slot.
     */
    public function validateSlot(int $classId, int $subjectId, int $teacherId, int $slotId, string $day, int $academicYearId = null): array
    {
        $errors = [];

        // 1. Class Conflict: Does the class already have a subject in this slot?
        if ($this->scheduleRepo->findConflict($slotId, $day, $classId, null, $academicYearId)) {
            $errors[] = "This class already has a subject scheduled for this time slot.";
        }

        // 2. Teacher Conflict: Is the teacher already teaching another class in this slot?
        if ($this->scheduleRepo->findConflict($slotId, $day, null, $teacherId, $academicYearId)) {
            $errors[] = "This teacher is already assigned to another class during this time slot.";
        }

        // 3. Eligibility: Is the teacher assigned to teach this subject to this class?
        $assignment = $this->taRepo->findOneBy([
            'teacher' => $teacherId,
            'subject' => $subjectId,
            'classe' => $classId
        ]);

        if (!$assignment) {
            $errors[] = "This teacher is not assigned to teach this subject to this class.";
        }

        return $errors;
    }
}
