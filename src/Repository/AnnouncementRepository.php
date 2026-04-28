<?php

namespace App\Repository;

use App\Entity\Announcement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Announcement>
 */
class AnnouncementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announcement::class);
    }

    /**
     * @return Announcement[]
     */
    public function findForStudent(\App\Entity\Classe $classe): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.target_type = :school')
            ->orWhere('a.target_type = :grade AND a.target_value = :gradeValue')
            ->orWhere('a.target_type = :class AND a.target_id = :classId')
            ->andWhere('a.publish_at <= :now')
            ->andWhere('a.expire_at IS NULL OR a.expire_at > :now')
            ->setParameter('school', 'SCHOOL')
            ->setParameter('grade', 'GRADE')
            ->setParameter('gradeValue', $classe->getGradeLevel())
            ->setParameter('class', 'CLASS')
            ->setParameter('classId', $classe->getId())
            ->setParameter('now', new \DateTime())
            ->orderBy('a.is_pinned', 'DESC')
            ->addOrderBy('a.publish_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
