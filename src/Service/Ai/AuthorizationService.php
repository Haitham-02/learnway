<?php

namespace App\Service\Ai;

use App\Entity\User;
use App\Entity\Classe;
use App\Entity\Chapter;
use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AuthorizationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    /**
     * Get IDs of classes the user is authorized to access.
     */
    public function getAuthorizedClassIds(User $user): array
    {
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles)) {
            $classes = $this->entityManager->getRepository(Classe::class)->findAll();
            return array_map(fn($c) => $c->getId(), $classes);
        }

        if (in_array('ROLE_TEACHER', $roles)) {
            // Get classes where teacher is assigned
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('c.id')
                ->from('App\Entity\TeacherAssignment', 'ta')
                ->join('ta.classe', 'c')
                ->where('ta.teacher = :user')
                ->setParameter('user', $user);
            
            return array_column($qb->getQuery()->getScalarResult(), 'id');
        }

        // Default: Student enrollment
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c.id')
            ->from('App\Entity\StudentEnrollment', 'se')
            ->join('se.classe', 'c')
            ->where('se.user = :user')
            ->setParameter('user', $user);

        return array_column($qb->getQuery()->getScalarResult(), 'id');
    }

    /**
     * Check if user can access a specific chapter/lesson.
     */
    public function canAccessChapter(User $user, Chapter $chapter): bool
    {
        $authorizedClassIds = $this->getAuthorizedClassIds($user);
        return in_array($chapter->getClasse()?->getId(), $authorizedClassIds);
    }
}
