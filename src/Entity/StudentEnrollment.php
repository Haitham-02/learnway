<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\StudentEnrollmentRepository;

#[ORM\Entity(repositoryClass: StudentEnrollmentRepository::class)]
#[ORM\Table(name: 'student_enrollments')]
#[ORM\UniqueConstraint(name: 'uniq_user_academic_year', columns: ['user_id', 'academic_year_id'])]
class StudentEnrollment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'studentEnrollments')]
    #[ORM\JoinColumn(name: 'class_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Classe $classe = null;

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'studentEnrollments')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: AcademicYear::class, inversedBy: 'studentEnrollments')]
    #[ORM\JoinColumn(name: 'academic_year_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?AcademicYear $academicYear = null;

    public function getAcademicYear(): ?AcademicYear
    {
        return $this->academicYear;
    }

    public function setAcademicYear(?AcademicYear $academicYear): self
    {
        $this->academicYear = $academicYear;
        return $this;
    }

}
