<?php

namespace App\Repository;

use App\Entity\StudentEnrollment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StudentEnrollment>
 */
class StudentEnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentEnrollment::class);
    }

    /**
     * @return StudentEnrollment[]
     */
    public function findForAdminList(
        ?string $query = null,
        ?int $classId = null,
        ?string $active = null,
        string $sort = 'newest',
    ): array {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.classe', 'c')->addSelect('c')
            ->leftJoin('e.user', 'u')->addSelect('u')
            ->leftJoin('e.academicYear', 'a')->addSelect('a');

        if ($query !== null && $query !== '') {
            $qb->andWhere('LOWER(c.name) LIKE :q OR LOWER(CONCAT(COALESCE(u.first_name, \'\'), \' \', COALESCE(u.last_name, \'\'))) LIKE :q OR LOWER(u.email) LIKE :q')
                ->setParameter('q', '%' . mb_strtolower($query) . '%');
        }
        if ($classId) {
            $qb->andWhere('c.id = :classId')->setParameter('classId', $classId);
        }


        if ($sort === 'oldest') {
            $qb->orderBy('e.id', 'ASC');

        } else {
            $qb->orderBy('e.id', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return StudentEnrollment[] Returns an array of StudentEnrollment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?StudentEnrollment
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
