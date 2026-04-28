<?php

namespace App\Repository;

use App\Entity\Assignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assignment>
 */
class AssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    /**
     * @return Assignment[]
     */
    public function findUpcomingForStudent(\App\Entity\Classe $classe, \App\Entity\User $user): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.chapter', 'c')
            ->leftJoin('a.submissions', 's', 'WITH', 's.student = :user')
            ->andWhere('c.classe = :classe')
            ->andWhere('a.status = :published')
            ->andWhere('a.due_date >= :now')
            ->andWhere('s.id IS NULL')
            ->setParameter('classe', $classe)
            ->setParameter('user', $user)
            ->setParameter('published', 'PUBLISHED')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.due_date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
