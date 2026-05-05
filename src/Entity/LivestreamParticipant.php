<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\LivestreamParticipantRepository;

#[ORM\Entity(repositoryClass: LivestreamParticipantRepository::class)]
#[ORM\Table(name: 'livestream_participants')]
#[ORM\UniqueConstraint(name: 'UNIQ_PARTICIPANT_SESSION', columns: ['livestream_id', 'user_id'])]
class LivestreamParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Livestream::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(name: 'livestream_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Livestream $livestream = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $role = 'STUDENT'; // TEACHER, STUDENT

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $joinedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $leftAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->joinedAt = new \DateTime();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getJoinedAt(): ?\DateTimeInterface
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeInterface $joinedAt): self
    {
        $this->joinedAt = $joinedAt;
        return $this;
    }

    public function getLeftAt(): ?\DateTimeInterface
    {
        return $this->leftAt;
    }

    public function setLeftAt(?\DateTimeInterface $leftAt): self
    {
        $this->leftAt = $leftAt;
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

    public function getDurationInMinutes(): int
    {
        $end = $this->leftAt ?? new \DateTime();
        $interval = $end->diff($this->joinedAt);
        return (int)$interval->i + ($interval->h * 60) + ($interval->d * 1440);
    }
}
