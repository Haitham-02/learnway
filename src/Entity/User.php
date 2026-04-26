<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
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

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: "users")]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
    private ?Role $role = null;

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: false)]
    private ?string $password_hash = null;

    public function getPassword_hash(): ?string
    {
        return $this->password_hash;
    }

    public function setPassword_hash(string $password_hash): self
    {
        $this->password_hash = $password_hash;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: false)]
    private ?string $first_name = null;

    public function getFirst_name(): ?string
    {
        return $this->first_name;
    }

    public function setFirst_name(string $first_name): self
    {
        $this->first_name = $first_name;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: false)]
    private ?string $last_name = null;

    public function getLast_name(): ?string
    {
        return $this->last_name;
    }

    public function setLast_name(string $last_name): self
    {
        $this->last_name = $last_name;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $profile_picture = null;

    public function getProfile_picture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfile_picture(?string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;
        return $this;
    }

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $date_of_birth = null;

    public function getDate_of_birth(): ?\DateTimeInterface
    {
        return $this->date_of_birth;
    }

    public function setDate_of_birth(?\DateTimeInterface $date_of_birth): self
    {
        $this->date_of_birth = $date_of_birth;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $gender = null;

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $phone = null;

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $employee_id = null;

    public function getEmployee_id(): ?string
    {
        return $this->employee_id;
    }

    public function setEmployee_id(?string $employee_id): self
    {
        $this->employee_id = $employee_id;
        return $this;
    }

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $student_id = null;

    public function getStudent_id(): ?string
    {
        return $this->student_id;
    }

    public function setStudent_id(?string $student_id): self
    {
        $this->student_id = $student_id;
        return $this;
    }

    #[ORM\Column(type: "boolean", nullable: true)]
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

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $last_login_at = null;

    public function getLast_login_at(): ?\DateTimeInterface
    {
        return $this->last_login_at;
    }

    public function setLast_login_at(?\DateTimeInterface $last_login_at): self
    {
        $this->last_login_at = $last_login_at;
        return $this;
    }

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    public function getCreated_at(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreated_at(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Announcement::class, mappedBy: "user")]
    private Collection $announcements;

    /**
     * @return Collection<int, Announcement>
     */
    public function getAnnouncements(): Collection
    {
        if (!$this->announcements instanceof Collection) {
            $this->announcements = new ArrayCollection();
        }
        return $this->announcements;
    }

    public function addAnnouncement(Announcement $announcement): self
    {
        if (!$this->getAnnouncements()->contains($announcement)) {
            $this->getAnnouncements()->add($announcement);
        }
        return $this;
    }

    public function removeAnnouncement(Announcement $announcement): self
    {
        $this->getAnnouncements()->removeElement($announcement);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: ChapterContent::class, mappedBy: "user")]
    private Collection $chapterContents;

    /**
     * @return Collection<int, ChapterContent>
     */
    public function getChapterContents(): Collection
    {
        if (!$this->chapterContents instanceof Collection) {
            $this->chapterContents = new ArrayCollection();
        }
        return $this->chapterContents;
    }

    public function addChapterContent(ChapterContent $chapterContent): self
    {
        if (!$this->getChapterContents()->contains($chapterContent)) {
            $this->getChapterContents()->add($chapterContent);
        }
        return $this;
    }

    public function removeChapterContent(ChapterContent $chapterContent): self
    {
        $this->getChapterContents()->removeElement($chapterContent);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: ChapterFile::class, mappedBy: "user")]
    private Collection $chapterFiles;

    /**
     * @return Collection<int, ChapterFile>
     */
    public function getChapterFiles(): Collection
    {
        if (!$this->chapterFiles instanceof Collection) {
            $this->chapterFiles = new ArrayCollection();
        }
        return $this->chapterFiles;
    }

    public function addChapterFile(ChapterFile $chapterFile): self
    {
        if (!$this->getChapterFiles()->contains($chapterFile)) {
            $this->getChapterFiles()->add($chapterFile);
        }
        return $this;
    }

    public function removeChapterFile(ChapterFile $chapterFile): self
    {
        $this->getChapterFiles()->removeElement($chapterFile);
        return $this;
    }

    #[ORM\OneToOne(targetEntity: ChapterProgress::class, mappedBy: "user")]
    private ?ChapterProgress $chapterProgress = null;

    public function getChapterProgress(): ?ChapterProgress
    {
        return $this->chapterProgress;
    }

    public function setChapterProgress(?ChapterProgress $chapterProgress): self
    {
        $this->chapterProgress = $chapterProgress;
        return $this;
    }

    #[ORM\OneToOne(targetEntity: StudentEnrollment::class, mappedBy: "user")]
    private ?StudentEnrollment $studentEnrollment = null;

    public function getStudentEnrollment(): ?StudentEnrollment
    {
        return $this->studentEnrollment;
    }

    public function setStudentEnrollment(?StudentEnrollment $studentEnrollment): self
    {
        $this->studentEnrollment = $studentEnrollment;
        return $this;
    }

    #[ORM\OneToOne(targetEntity: ConversationMember::class, mappedBy: "user")]
    private ?ConversationMember $conversationMember = null;

    public function getConversationMember(): ?ConversationMember
    {
        return $this->conversationMember;
    }

    public function setConversationMember(
        ?ConversationMember $conversationMember,
    ): self {
        $this->conversationMember = $conversationMember;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: "user")]
    private Collection $conversations;

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        if (!$this->conversations instanceof Collection) {
            $this->conversations = new ArrayCollection();
        }
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->getConversations()->contains($conversation)) {
            $this->getConversations()->add($conversation);
        }
        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        $this->getConversations()->removeElement($conversation);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: ForumComment::class, mappedBy: "user")]
    private Collection $forumComments;

    /**
     * @return Collection<int, ForumComment>
     */
    public function getForumComments(): Collection
    {
        if (!$this->forumComments instanceof Collection) {
            $this->forumComments = new ArrayCollection();
        }
        return $this->forumComments;
    }

    public function addForumComment(ForumComment $forumComment): self
    {
        if (!$this->getForumComments()->contains($forumComment)) {
            $this->getForumComments()->add($forumComment);
        }
        return $this;
    }

    public function removeForumComment(ForumComment $forumComment): self
    {
        $this->getForumComments()->removeElement($forumComment);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: ForumPost::class, mappedBy: "user")]
    private Collection $forumPosts;

    /**
     * @return Collection<int, ForumPost>
     */
    public function getForumPosts(): Collection
    {
        if (!$this->forumPosts instanceof Collection) {
            $this->forumPosts = new ArrayCollection();
        }
        return $this->forumPosts;
    }

    public function addForumPost(ForumPost $forumPost): self
    {
        if (!$this->getForumPosts()->contains($forumPost)) {
            $this->getForumPosts()->add($forumPost);
        }
        return $this;
    }

    public function removeForumPost(ForumPost $forumPost): self
    {
        $this->getForumPosts()->removeElement($forumPost);
        return $this;
    }

    #[ORM\OneToOne(targetEntity: ForumReview::class, mappedBy: "user")]
    private ?ForumReview $forumReview = null;

    public function getForumReview(): ?ForumReview
    {
        return $this->forumReview;
    }

    public function setForumReview(?ForumReview $forumReview): self
    {
        $this->forumReview = $forumReview;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: "user")]
    private Collection $messages;

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        if (!$this->messages instanceof Collection) {
            $this->messages = new ArrayCollection();
        }
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->getMessages()->contains($message)) {
            $this->getMessages()->add($message);
        }
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        $this->getMessages()->removeElement($message);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: SubjectSection::class, mappedBy: "user")]
    private Collection $subjectSections;

    /**
     * @return Collection<int, SubjectSection>
     */
    public function getSubjectSections(): Collection
    {
        if (!$this->subjectSections instanceof Collection) {
            $this->subjectSections = new ArrayCollection();
        }
        return $this->subjectSections;
    }

    public function addSubjectSection(SubjectSection $subjectSection): self
    {
        if (!$this->getSubjectSections()->contains($subjectSection)) {
            $this->getSubjectSections()->add($subjectSection);
        }
        return $this;
    }

    public function removeSubjectSection(SubjectSection $subjectSection): self
    {
        $this->getSubjectSections()->removeElement($subjectSection);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: SubmissionFile::class, mappedBy: "user")]
    private Collection $submissionFiles;

    /**
     * @return Collection<int, SubmissionFile>
     */
    public function getSubmissionFiles(): Collection
    {
        if (!$this->submissionFiles instanceof Collection) {
            $this->submissionFiles = new ArrayCollection();
        }
        return $this->submissionFiles;
    }

    public function addSubmissionFile(SubmissionFile $submissionFile): self
    {
        if (!$this->getSubmissionFiles()->contains($submissionFile)) {
            $this->getSubmissionFiles()->add($submissionFile);
        }
        return $this;
    }

    public function removeSubmissionFile(SubmissionFile $submissionFile): self
    {
        $this->getSubmissionFiles()->removeElement($submissionFile);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Submission::class, mappedBy: "student")]
    private Collection $submissions;

    /**
     * @return Collection<int, Submission>
     */
    public function getSubmissions(): Collection
    {
        if (!$this->submissions instanceof Collection) {
            $this->submissions = new ArrayCollection();
        }
        return $this->submissions;
    }

    public function addSubmission(Submission $submission): self
    {
        if (!$this->getSubmissions()->contains($submission)) {
            $this->getSubmissions()->add($submission);
        }
        return $this;
    }

    public function removeSubmission(Submission $submission): self
    {
        $this->getSubmissions()->removeElement($submission);
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Message::class, inversedBy: "users")]
    #[
        ORM\JoinTable(
            name: "message_reads",
            joinColumns: [
                new ORM\JoinColumn(name: "user_id", referencedColumnName: "id"),
            ],
            inverseJoinColumns: [
                new ORM\JoinColumn(
                    name: "message_id",
                    referencedColumnName: "id",
                ),
            ],
        ),
    ]
    private Collection $readMessages;

    public function __construct()
    {
        $this->announcements = new ArrayCollection();
        $this->chapterContents = new ArrayCollection();
        $this->chapterFiles = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->forumComments = new ArrayCollection();
        $this->forumPosts = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->subjectSections = new ArrayCollection();
        $this->submissionFiles = new ArrayCollection();
        $this->submissions = new ArrayCollection();
        $this->readMessages = new ArrayCollection();
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReadMessages(): Collection
    {
        if (!$this->readMessages instanceof Collection) {
            $this->readMessages = new ArrayCollection();
        }
        return $this->readMessages;
    }

    public function addReadMessage(Message $message): self
    {
        if (!$this->getReadMessages()->contains($message)) {
            $this->getReadMessages()->add($message);
        }
        return $this;
    }

    public function removeReadMessage(Message $message): self
    {
        $this->getReadMessages()->removeElement($message);
        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    public function setPasswordHash(string $password_hash): static
    {
        $this->password_hash = $password_hash;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfilePicture(?string $profile_picture): static
    {
        $this->profile_picture = $profile_picture;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(?\DateTime $date_of_birth): static
    {
        $this->date_of_birth = $date_of_birth;

        return $this;
    }

    public function getEmployeeId(): ?string
    {
        return $this->employee_id;
    }

    public function setEmployeeId(?string $employee_id): static
    {
        $this->employee_id = $employee_id;

        return $this;
    }

    public function getStudentId(): ?string
    {
        return $this->student_id;
    }

    public function setStudentId(?string $student_id): static
    {
        $this->student_id = $student_id;

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

    public function getLastLoginAt(): ?\DateTime
    {
        return $this->last_login_at;
    }

    public function setLastLoginAt(?\DateTime $last_login_at): static
    {
        $this->last_login_at = $last_login_at;

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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roleName = $this->role?->getName();
        $symfonyRole = $roleName
            ? 'ROLE_' . strtoupper(str_replace(' ', '_', $roleName))
            : 'ROLE_USER';

        return array_unique([$symfonyRole, 'ROLE_USER']);
    }

    public function getPassword(): ?string
    {
        return $this->password_hash;
    }

    public function eraseCredentials(): void {}
}
