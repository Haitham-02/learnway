<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeacherAssignmentRepository;

#[ORM\Entity(repositoryClass: TeacherAssignmentRepository::class)]
#[ORM\Table(name: 'teacher_assignments')]
#[ORM\UniqueConstraint(name: 'uniq_teacher_subject_class', columns: ['teacher_id', 'subject_id', 'class_id'])]
class TeacherAssignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $teacher = null;

    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(name: 'subject_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(targetEntity: Classe::class)]
    #[ORM\JoinColumn(name: 'class_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Classe $classe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;
        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;
        return $this;
    }
}
