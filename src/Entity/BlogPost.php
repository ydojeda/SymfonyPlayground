<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 */
class BlogPost implements \JsonSerializable
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
    private $body = '';

    /**
     * @ORM\Column(type="text")
     */
    private $tags = '';

    /**
     * @ORM\Column(type="integer")
     */
    private $reactions = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDate;

    /**
     * @ORM\ManyToOne(targetEntity=BlogUser::class, inversedBy="blogPosts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

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

    public function getCreateDate(): \DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getCreatedBy(): BlogUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?BlogUser $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
