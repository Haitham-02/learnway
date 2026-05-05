<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\LivestreamRepository;

#[ORM\Entity(repositoryClass: LivestreamRepository::class)]
#[ORM\Table(name: 'livestreams')]
class Livestream
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $teacher = null;

    #[ORM\ManyToOne(targetEntity: Classe::class)]
    #[ORM\JoinColumn(name: 'class_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Classe $classe = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $meetingRoom = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $scheduledAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endedAt = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = 'SCHEDULED'; // SCHEDULED, LIVE, ENDED

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $recordingUrl = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(targetEntity: LivestreamParticipant::class, mappedBy: 'livestream', cascade: ['remove'])]
    private Collection $participants;

    #[ORM\OneToMany(targetEntity: LivestreamQA::class, mappedBy: 'livestream', cascade: ['remove'])]
    private Collection $questions;

    #[ORM\OneToMany(targetEntity: FacialAnalysis::class, mappedBy: 'livestream', cascade: ['remove'])]
    private Collection $facialAnalyses;

    #[ORM\OneToMany(targetEntity: LivestreamChat::class, mappedBy: 'livestream', cascade: ['remove'])]
    private Collection $chats;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->facialAnalyses = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters and Setters
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

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getMeetingRoom(): ?string
    {
        return $this->meetingRoom;
    }

    public function setMeetingRoom(string $meetingRoom): self
    {
        $this->meetingRoom = $meetingRoom;
        return $this;
    }

    public function getScheduledAt(): ?\DateTimeInterface
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(?\DateTimeInterface $scheduledAt): self
    {
        $this->scheduledAt = $scheduledAt;
        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getRecordingUrl(): ?string
    {
        return $this->recordingUrl;
    }

    public function setRecordingUrl(?string $recordingUrl): self
    {
        $this->recordingUrl = $recordingUrl;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(LivestreamParticipant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setLivestream($this);
        }
        return $this;
    }

    public function removeParticipant(LivestreamParticipant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            if ($participant->getLivestream() === $this) {
                $participant->setLivestream(null);
            }
        }
        return $this;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(LivestreamQA $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setLivestream($this);
        }
        return $this;
    }

    public function getFacialAnalyses(): Collection
    {
        return $this->facialAnalyses;
    }

    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(LivestreamChat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats->add($chat);
            $chat->setLivestream($this);
        }
        return $this;
    }

    public function isLive(): bool
    {
        return $this->status === 'LIVE';
    }

    public function canJoin(): bool
    {
        if ($this->status === 'LIVE') {
            return true;
        }
        if ($this->status === 'SCHEDULED' && $this->scheduledAt) {
            return $this->scheduledAt <= new \DateTime();
        }
        return false;
    }
}
