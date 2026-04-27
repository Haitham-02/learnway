<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[]
     */
    public function findStudentsForEnrollment(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r')
            ->andWhere('UPPER(r.name) = :role')
            ->setParameter('role', 'STUDENT')
            ->orderBy('u.is_active', 'DESC')
            ->addOrderBy('u.last_name', 'ASC')
            ->addOrderBy('u.first_name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findStudentForEnrollmentById(int $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r')
            ->andWhere('u.id = :id')
            ->andWhere('UPPER(r.name) = :role')
            ->setParameter('id', $id)
            ->setParameter('role', 'STUDENT')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function findTeachers(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r')
            ->andWhere('UPPER(r.name) = :role')
            ->setParameter('role', 'TEACHER')
            ->orderBy('u.is_active', 'DESC')
            ->addOrderBy('u.last_name', 'ASC')
            ->addOrderBy('u.first_name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTeacherById(int $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r')
            ->andWhere('u.id = :id')
            ->andWhere('UPPER(r.name) = :role')
            ->setParameter('id', $id)
            ->setParameter('role', 'TEACHER')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function findForAuthorSelector(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r')
            ->orderBy('u.is_active', 'DESC')
            ->addOrderBy('u.last_name', 'ASC')
            ->addOrderBy('u.first_name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function countByRole(string $roleName): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->leftJoin('u.role', 'r')
            ->andWhere('UPPER(r.name) = :role')
            ->setParameter('role', strtoupper($roleName))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
