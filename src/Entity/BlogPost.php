<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 */
class BlogPost
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reactions;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $createDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        if ($body !== null) {
            $this->body = $body;
        }

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        if ($tags !== null) {
            $this->tags = $tags;
        }

        return $this;
    }

    public function getReactions(): ?int
    {
        return $this->reactions;
    }

    public function setReactions(?int $reactions): self
    {
        if ($reactions !== null) {
            $this->reactions = $reactions;
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreateDate(): ?int
    {
        return $this->createDate;
    }

    public function setCreateDate(int $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }
}
