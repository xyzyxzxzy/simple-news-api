<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\NewsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\SerializedName;


#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    public const PATH_TO_SAVE = '/uploads/news/';
    public const PREVIEW_WIDTH = 400;
    public const PREVIEW_HEIGHT = 400;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $preview;

    #[ORM\Column(type: 'date')]
    private DateTime $dateCreation;

    #[ORM\Column(type: 'date', nullable: true)]
    private DateTime $datePublication;

    #[SerializedName('tags')]
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'news')]
    private Collection $tag;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\JoinTable(name: 'news_user_likes')]
    #[ORM\JoinColumn(name: 'news_id', referencedColumnName: 'id', unique: true)]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id', unique: true)]
    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $likes;

    public function __construct()
    {
        $this->dateCreation = new DateTime();
        $this->tag = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPreview(): ?string
    {
        return $this->preview;
    }

    public function setPreview(string $preview): self
    {
        $this->preview = $preview;

        return $this;
    }

    public function getDateCreation(): DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(DateTime $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDatePublication(): ?DateTime
    {
        return $this->datePublication;
    }

    public function setDatePublication(DateTime $datePublication): self
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tag->contains($tag)) {
            $this->tag[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tag->removeElement($tag);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }
}
