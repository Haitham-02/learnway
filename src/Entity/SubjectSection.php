<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\SubjectSectionRepository;

#[ORM\Entity(repositoryClass: SubjectSectionRepository::class)]
#[ORM\Table(name: 'subject_sections')]
class SubjectSection
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

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'subjectSections')]
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

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'subjectSections')]
    #[ORM\JoinColumn(name: 'subject_id', referencedColumnName: 'id')]
    private ?Subject $subject = null;

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Term::class, inversedBy: 'subjectSections')]
    #[ORM\JoinColumn(name: 'term_id', referencedColumnName: 'id')]
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'subjectSections')]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $room_number = null;

    public function getRoom_number(): ?string
    {
        return $this->room_number;
    }

    public function setRoom_number(?string $room_number): self
    {
        $this->room_number = $room_number;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $assigned_at = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $ended_at = null;

    public function getCreated_at(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreated_at(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getAssigned_at(): ?\DateTimeInterface
    {
        return $this->assigned_at;
    }

    public function setAssigned_at(?\DateTimeInterface $assigned_at): self
    {
        $this->assigned_at = $assigned_at;
        return $this;
    }

    public function getEnded_at(): ?\DateTimeInterface
    {
        return $this->ended_at;
    }

    public function setEnded_at(?\DateTimeInterface $ended_at): self
    {
        $this->ended_at = $ended_at;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Chapter::class, mappedBy: 'subjectSection')]
    private Collection $chapters;

    public function __construct()
    {
        $this->chapters = new ArrayCollection();
    }

    /**
     * @return Collection<int, Chapter>
     */
    public function getChapters(): Collection
    {
        if (!$this->chapters instanceof Collection) {
            $this->chapters = new ArrayCollection();
        }
        return $this->chapters;
    }

    public function addChapter(Chapter $chapter): self
    {
        if (!$this->getChapters()->contains($chapter)) {
            $this->getChapters()->add($chapter);
        }
        return $this;
    }

    public function removeChapter(Chapter $chapter): self
    {
        $this->getChapters()->removeElement($chapter);
        return $this;
    }

    public function getRoomNumber(): ?string
    {
        return $this->room_number;
    }

    public function setRoomNumber(?string $room_number): static
    {
        $this->room_number = $room_number;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAssignedAt(): ?\DateTime
    {
        return $this->assigned_at;
    }

    public function setAssignedAt(?\DateTime $assigned_at): static
    {
        $this->assigned_at = $assigned_at;

        return $this;
    }

    public function getEndedAt(): ?\DateTime
    {
        return $this->ended_at;
    }

    public function setEndedAt(?\DateTime $ended_at): static
    {
        $this->ended_at = $ended_at;

        return $this;
    }

}
