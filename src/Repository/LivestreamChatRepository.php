<?php

namespace App\Repository;

use App\Entity\LivestreamChat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivestreamChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivestreamChat::class);
    }

    public function findByLivestream($livestreamId, $limit = 50)
    {
        return $this->createQueryBuilder('lc')
            ->where('lc.livestream = :livestreamId')
            ->setParameter('livestreamId', $livestreamId)
            ->orderBy('lc.createdAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countByLivestream($livestreamId)
    {
        return $this->createQueryBuilder('lc')
            ->select('COUNT(lc.id)')
            ->where('lc.livestream = :livestreamId')
            ->setParameter('livestreamId', $livestreamId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
