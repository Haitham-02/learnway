<?php

namespace App\Repository;

use App\Entity\LivestreamParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivestreamParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivestreamParticipant::class);
    }

    public function countActiveParticipants($livestreamId)
    {
        return $this->createQueryBuilder('lp')
            ->select('COUNT(lp.id)')
            ->where('lp.livestream = :livestreamId')
            ->andWhere('lp.leftAt IS NULL')
            ->setParameter('livestreamId', $livestreamId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findActiveParticipants($livestreamId)
    {
        return $this->createQueryBuilder('lp')
            ->where('lp.livestream = :livestreamId')
            ->andWhere('lp.leftAt IS NULL')
            ->setParameter('livestreamId', $livestreamId)
            ->orderBy('lp.joinedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
