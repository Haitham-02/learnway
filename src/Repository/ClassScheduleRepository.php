<?php

namespace App\Repository;

use App\Entity\ClassSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClassScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassSchedule::class);
    }

    public function findByClass(int $classId)
    {
        return $this->createQueryBuilder('s')
            ->join('s.timeSlot', 'ts')
            ->where('s.classe = :classId')
            ->setParameter('classId', $classId)
            ->orderBy('ts.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByTeacher(int $teacherId)
    {
        return $this->createQueryBuilder('s')
            ->join('s.timeSlot', 'ts')
            ->where('s.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->orderBy('ts.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findConflict(int $slotId, string $day, int $classId = null, int $teacherId = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.timeSlot = :slotId')
            ->andWhere('s.dayOfWeek = :day')
            ->setParameter('slotId', $slotId)
            ->setParameter('day', $day);

        if ($classId) {
            $qb->andWhere('s.classe = :classId')->setParameter('classId', $classId);
        } elseif ($teacherId) {
            $qb->andWhere('s.teacher = :teacherId')->setParameter('teacherId', $teacherId);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}
