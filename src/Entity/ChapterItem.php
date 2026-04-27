<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ChapterItemRepository;

#[ORM\Entity(repositoryClass: ChapterItemRepository::class)]
#[ORM\Table(name: 'chapter_items')]
class ChapterItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
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

    #[ORM\ManyToOne(targetEntity: Chapter::class, inversedBy: 'chapterItems')]
    #[ORM\JoinColumn(name: 'chapter_id', referencedColumnName: 'id')]
    private ?Chapter $chapter = null;

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(?Chapter $chapter): self
    {
        $this->chapter = $chapter;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $url = null;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sort_order = null;

    public function getSort_order(): ?int
    {
        return $this->sort_order;
    }

    public function setSort_order(?int $sort_order): self
    {
        $this->sort_order = $sort_order;
        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sort_order;
    }

    public function setSortOrder(?int $sort_order): static
    {
        $this->sort_order = $sort_order;

        return $this;
    }

}
