<?php

namespace App\Entity;

use App\Repository\ClassScheduleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ClassScheduleRepository::class)]
#[ORM\Table(name: 'class_schedules')]
#[ORM\UniqueConstraint(name: 'uniq_class_slot_day_year', columns: ['classe_id', 'time_slot_id', 'day_of_week', 'academic_year_id'])]
#[ORM\UniqueConstraint(name: 'uniq_teacher_slot_day_year', columns: ['teacher_id', 'time_slot_id', 'day_of_week', 'academic_year_id'])]
#[UniqueEntity(fields: ['classe', 'timeSlot', 'dayOfWeek', 'academicYear'], message: 'This class already has a subject at this time for this year.')]
#[UniqueEntity(fields: ['teacher', 'timeSlot', 'dayOfWeek', 'academicYear'], message: 'This teacher is already teaching another class at this time for this year.')]
class ClassSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Classe::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Classe $classe = null;

    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $teacher = null;

    #[ORM\ManyToOne(targetEntity: TimeSlot::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?TimeSlot $timeSlot = null;

    #[ORM\Column(length: 20)]
    private ?string $dayOfWeek = null; // Monday, Tuesday, etc.

    #[ORM\ManyToOne(targetEntity: AcademicYear::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?AcademicYear $academicYear = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;
        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;
        return $this;
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

    public function getTimeSlot(): ?TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(?TimeSlot $timeSlot): static
    {
        $this->timeSlot = $timeSlot;
        return $this;
    }

    public function getDayOfWeek(): ?string
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(string $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function getAcademicYear(): ?AcademicYear
    {
        return $this->academicYear;
    }

    public function setAcademicYear(?AcademicYear $academicYear): static
    {
        $this->academicYear = $academicYear;
        return $this;
    }
}
