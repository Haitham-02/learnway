<?php

namespace App\Entity;

use App\Repository\AiKnowledgeBaseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AiKnowledgeBaseRepository::class)]
#[ORM\Table(name: 'ai_knowledge_base')]
class AiKnowledgeBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $sourceType = null; // lesson, chapter_file, etc.

    #[ORM\Column(type: 'bigint')]
    private ?int $sourceId = null;

    #[ORM\Column(length: 255)]
    private ?string $vectorId = null; // UUID in Qdrant

    #[ORM\Column]
    private ?\DateTimeImmutable $indexedAt = null;

    public function __construct()
    {
        $this->indexedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceType(): ?string
    {
        return $this->sourceType;
    }

    public function setSourceType(string $sourceType): static
    {
        $this->sourceType = $sourceType;
        return $this;
    }

    public function getSourceId(): ?int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): static
    {
        $this->sourceId = $sourceId;
        return $this;
    }

    public function getVectorId(): ?string
    {
        return $this->vectorId;
    }

    public function setVectorId(string $vectorId): static
    {
        $this->vectorId = $vectorId;
        return $this;
    }

    public function getIndexedAt(): ?\DateTimeImmutable
    {
        return $this->indexedAt;
    }

    public function setIndexedAt(\DateTimeImmutable $indexedAt): static
    {
        $this->indexedAt = $indexedAt;
        return $this;
    }
}
