<?php

namespace App\Repository;

use App\Entity\Chapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chapter>
 */
class ChapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chapter::class);
    }

    /**
     * @return Chapter[]
     */
    public function findForAdminIndex(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.classe', 'cl')
            ->addSelect('cl')
            ->leftJoin('c.subject', 'su')
            ->addSelect('su')
            ->addSelect('CASE WHEN c.sort_order IS NULL THEN 1 ELSE 0 END AS HIDDEN sort_order_is_null')
            ->orderBy('cl.name', 'ASC')
            ->addOrderBy('su.name', 'ASC')
            ->addOrderBy('sort_order_is_null', 'ASC')
            ->addOrderBy('c.sort_order', 'ASC')
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
