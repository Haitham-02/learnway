<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\FacialAnalysisRepository;

#[ORM\Entity(repositoryClass: FacialAnalysisRepository::class)]
#[ORM\Table(name: 'facial_analysis')]
class FacialAnalysis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Livestream::class, inversedBy: 'facialAnalyses')]
    #[ORM\JoinColumn(name: 'livestream_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Livestream $livestream = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $student = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $emotion = null; // happy, sad, angry, confused, distracted, etc.

    #[ORM\Column(type: 'decimal', precision: 5, scale: 4)]
    private ?string $confidence = null; // 0.0 - 1.0

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $additionalData = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivestream(): ?Livestream
    {
        return $this->livestream;
    }

    public function setLivestream(?Livestream $livestream): self
    {
        $this->livestream = $livestream;
        return $this;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): self
    {
        $this->student = $student;
        return $this;
    }

    public function getEmotion(): ?string
    {
        return $this->emotion;
    }

    public function setEmotion(string $emotion): self
    {
        $this->emotion = $emotion;
        return $this;
    }

    public function getConfidence(): ?string
    {
        return $this->confidence;
    }

    public function setConfidence($confidence): self
    {
        $this->confidence = (string)$confidence;
        return $this;
    }

    public function getConfidenceAsFloat(): float
    {
        return (float)$this->confidence;
    }

    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }

    public function setAdditionalData(?array $additionalData): self
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
