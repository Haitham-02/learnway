<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * Finds a peer-to-peer conversation between two users.
     */
    public function findPeerToPeerConversation(int $user1Id, int $user2Id): ?Conversation
    {
        // Sort IDs to ensure consistent hash regardless of who started the conversation
        $ids = [$user1Id, $user2Id];
        sort($ids);
        $hash = md5(implode('_', $ids));

        return $this->findOneBy(['pair_hash' => $hash]);
    }

    /**
     * Lists all conversations for a specific user.
     */
    public function findConversationsForUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.members', 'm')
            ->where('m.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Conversation[] Returns an array of Conversation objects
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

    //    public function findOneBySomeField($value): ?Conversation
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
