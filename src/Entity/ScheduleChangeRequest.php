<?php

namespace App\Entity;

use App\Repository\ScheduleChangeRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleChangeRequestRepository::class)]
#[ORM\Table(name: 'schedule_change_requests')]
class ScheduleChangeRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $teacher = null;

    #[ORM\ManyToOne(targetEntity: ClassSchedule::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ClassSchedule $classSchedule = null;

    #[ORM\ManyToOne(targetEntity: TimeSlot::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?TimeSlot $proposedTimeSlot = null;

    #[ORM\Column(length: 20)]
    private ?string $proposedDayOfWeek = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(length: 20)]
    private ?string $status = 'PENDING'; // PENDING, APPROVED, REJECTED

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): static
    {
        $this->teacher = $teacher;
        return $this;
    }

    public function getClassSchedule(): ?ClassSchedule
    {
        return $this->classSchedule;
    }

    public function setClassSchedule(?ClassSchedule $classSchedule): static
    {
        $this->classSchedule = $classSchedule;
        return $this;
    }

    public function getProposedTimeSlot(): ?TimeSlot
    {
        return $this->proposedTimeSlot;
    }

    public function setProposedTimeSlot(?TimeSlot $proposedTimeSlot): static
    {
        $this->proposedTimeSlot = $proposedTimeSlot;
        return $this;
    }

    public function getProposedDayOfWeek(): ?string
    {
        return $this->proposedDayOfWeek;
    }

    public function setProposedDayOfWeek(string $proposedDayOfWeek): static
    {
        $this->proposedDayOfWeek = $proposedDayOfWeek;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
