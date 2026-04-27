<?php

namespace App\Repository;

use App\Entity\TeacherAssignment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeacherAssignment>
 */
class TeacherAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeacherAssignment::class);
    }

    /**
     * @return TeacherAssignment[]
     */
    public function findByTeacher(User $teacher): array
    {
        return $this->createQueryBuilder('ta')
            ->leftJoin('ta.subject', 's')->addSelect('s')
            ->leftJoin('ta.classe', 'c')->addSelect('c')
            ->where('ta.teacher = :teacher')
            ->setParameter('teacher', $teacher)
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TeacherAssignment[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('ta')
            ->leftJoin('ta.teacher', 't')->addSelect('t')
            ->leftJoin('ta.subject', 's')->addSelect('s')
            ->leftJoin('ta.classe', 'c')->addSelect('c')
            ->orderBy('t.last_name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
