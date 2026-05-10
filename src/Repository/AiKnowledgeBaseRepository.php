<?php

namespace App\Repository;

use App\Entity\AiKnowledgeBase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AiKnowledgeBase>
 */
class AiKnowledgeBaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AiKnowledgeBase::class);
    }
}
