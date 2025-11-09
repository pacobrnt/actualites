<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 512)]
    private ?string $url = null;

    #[ORM\Column(length: 512)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $likedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }

    public function setSourceName(?string $sourceName): static
    {
        $this->sourceName = $sourceName;

        return $this;
    }

    public function getLikedAt(): ?\DateTimeImmutable
    {
        return $this->likedAt;
    }

    public function setLikedAt(\DateTimeImmutable $likedAt): static
    {
        $this->likedAt = $likedAt;

        return $this;
    }
}
