<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\SubjectRepository;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
#[ORM\Table(name: 'subjects')]
class Subject
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $subject_code = null;

    public function getSubject_code(): ?string
    {
        return $this->subject_code;
    }

    public function setSubject_code(string $subject_code): self
    {
        $this->subject_code = $subject_code;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $grade_level = null;

    public function getGradeLevel(): ?string
    {
        return $this->grade_level;
    }

    public function setGradeLevel(?string $grade_level): static
    {
        $this->grade_level = $grade_level;

        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $is_active = null;

    public function is_active(): ?bool
    {
        return $this->is_active;
    }

    public function setIs_active(?bool $is_active): self
    {
        $this->is_active = $is_active;
        return $this;
    }

    public function getSubjectCode(): ?string
    {
        return $this->subject_code;
    }

    public function setSubjectCode(string $subject_code): static
    {
        $this->subject_code = $subject_code;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(?bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Term::class)]
    #[ORM\JoinColumn(name: 'term_id', referencedColumnName: 'id', nullable: true)]
    private ?Term $term = null;

    public function getTerm(): ?Term
    {
        return $this->term;
    }

    public function setTerm(?Term $term): self
    {
        $this->term = $term;
        return $this;
    }

}
