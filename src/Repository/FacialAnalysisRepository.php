<?php

namespace App\Repository;

use App\Entity\FacialAnalysis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FacialAnalysisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacialAnalysis::class);
    }

    public function findByLivestream($livestreamId)
    {
        return $this->createQueryBuilder('fa')
            ->where('fa.livestream = :livestreamId')
            ->setParameter('livestreamId', $livestreamId)
            ->orderBy('fa.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getEmotionStats($livestreamId)
    {
        return $this->createQueryBuilder('fa')
            ->select('fa.emotion, COUNT(fa.id) as count, AVG(fa.confidence) as avgConfidence')
            ->where('fa.livestream = :livestreamId')
            ->setParameter('livestreamId', $livestreamId)
            ->groupBy('fa.emotion')
            ->getQuery()
            ->getResult();
    }

    public function getLatestEmotionByStudent($livestreamId, $studentId)
    {
        return $this->createQueryBuilder('fa')
            ->where('fa.livestream = :livestreamId')
            ->andWhere('fa.student = :studentId')
            ->setParameter('livestreamId', $livestreamId)
            ->setParameter('studentId', $studentId)
            ->orderBy('fa.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
