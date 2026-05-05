<?php

namespace App\Repository;

use App\Entity\LivestreamQA;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivestreamQARepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivestreamQA::class);
    }

    public function findByLivestream($livestreamId)
    {
        return $this->createQueryBuilder('qa')
            ->where('qa.livestream = :livestreamId')
            ->setParameter('livestreamId', $livestreamId)
            ->orderBy('qa.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countUnanswered($livestreamId)
    {
        return $this->createQueryBuilder('qa')
            ->select('COUNT(qa.id)')
            ->where('qa.livestream = :livestreamId')
            ->andWhere('qa.answer IS NULL')
            ->setParameter('livestreamId', $livestreamId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
