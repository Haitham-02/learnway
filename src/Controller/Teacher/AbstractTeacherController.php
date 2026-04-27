<?php

namespace App\Controller\Teacher;

use App\Entity\TeacherAssignment;
use App\Repository\TeacherAssignmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractTeacherController extends AbstractController
{
    protected function getTeacherAssignment(TeacherAssignmentRepository $repo, int $subjectId, int $classId): TeacherAssignment
    {
        $ta = $repo->findOneBy([
            'teacher' => $this->getUser(),
            'subject' => $subjectId,
            'classe' => $classId
        ]);

        if (!$ta) {
            throw $this->createAccessDeniedException('You are not assigned to this subject/class combination.');
        }

        return $ta;
    }

    protected function denyUnlessTeacherOwnsClass(TeacherAssignmentRepository $repo, int $classId): void
    {
        $assignment = $repo->findOneBy([
            'teacher' => $this->getUser(),
            'classe' => $classId
        ]);

        if (!$assignment) {
            throw $this->createAccessDeniedException('You do not teach this class.');
        }
    }
}
