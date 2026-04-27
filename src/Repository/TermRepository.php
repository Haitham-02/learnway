<?php

namespace App\Repository;

use App\Entity\Term;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Term>
 */
class TermRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Term::class);
    }

    /**
     * @return Term[]
     */
    public function findForSelector(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.academicYear', 'a')
            ->addSelect('a')
            ->orderBy('a.is_current', 'DESC')
            ->addOrderBy('t.is_current', 'DESC')
            ->addOrderBy('t.start_date', 'DESC')
            ->addOrderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Term[] Returns an array of Term objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Term
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
