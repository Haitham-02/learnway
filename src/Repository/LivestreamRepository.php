<?php

namespace App\Repository;

use App\Entity\Livestream;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivestreamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livestream::class);
    }

    public function findTeacherLivestreams($teacherId)
    {
        return $this->createQueryBuilder('l')
            ->where('l.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->orderBy('l.scheduledAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findStudentLivestreams($studentId)
    {
        return $this->createQueryBuilder('l')
            ->join('l.classe', 'c')
            ->join('App\Entity\StudentEnrollment', 'se', 'WITH', 'se.classe = c')
            ->where('se.user = :studentId')
            ->setParameter('studentId', $studentId)
            ->orderBy('l.scheduledAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingLivestreams()
    {
        return $this->createQueryBuilder('l')
            ->where('l.status = :status')
            ->andWhere('l.scheduledAt > :now')
            ->setParameter('status', 'SCHEDULED')
            ->setParameter('now', new \DateTime())
            ->orderBy('l.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findLiveNow()
    {
        return $this->createQueryBuilder('l')
            ->where('l.status = :status')
            ->setParameter('status', 'LIVE')
            ->getQuery()
            ->getResult();
    }

    public function generateUniqueMeetingRoom(): string
    {
        do {
            $room = 'lw_' . bin2hex(random_bytes(8));
            $existing = $this->findBy(['meetingRoom' => $room]);
        } while (!empty($existing));

        return $room;
    }
}
