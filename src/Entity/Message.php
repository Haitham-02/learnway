<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\MessageRepository;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
class Message
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

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'conversation_id', referencedColumnName: 'id')]
    private ?Conversation $conversation = null;

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'sender_id', referencedColumnName: 'id')]
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

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $content = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $is_deleted = null;

    public function is_deleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIs_deleted(?bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sent_at = null;

    public function getSent_at(): ?\DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSent_at(?\DateTimeInterface $sent_at): self
    {
        $this->sent_at = $sent_at;
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
    private ?\DateTimeInterface $delivered_at = null;

    public function getDelivered_at(): ?\DateTimeInterface
    {
        return $this->delivered_at;
    }

    public function setDelivered_at(?\DateTimeInterface $delivered_at): self
    {
        $this->delivered_at = $delivered_at;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $seen_at = null;

    public function getSeen_at(): ?\DateTimeInterface
    {
        return $this->seen_at;
    }

    public function setSeen_at(?\DateTimeInterface $seen_at): self
    {
        $this->seen_at = $seen_at;
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'readMessages')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        if (!$this->users instanceof Collection) {
            $this->users = new ArrayCollection();
        }
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->getUsers()->contains($user)) {
            $this->getUsers()->add($user);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->getUsers()->removeElement($user);
        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(?bool $is_deleted): static
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sent_at;
    }

    public function setSentAt(?\DateTime $sent_at): static
    {
        $this->sent_at = $sent_at;

        return $this;
    }

    public function getDeliveredAt(): ?\DateTime
    {
        return $this->delivered_at;
    }

    public function setDeliveredAt(?\DateTime $delivered_at): static
    {
        $this->delivered_at = $delivered_at;

        return $this;
    }

    public function getSeenAt(): ?\DateTime
    {
        return $this->seen_at;
    }

    public function setSeenAt(?\DateTime $seen_at): static
    {
        $this->seen_at = $seen_at;

        return $this;
    }

}
