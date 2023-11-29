<?php

namespace App\Entity;

use App\Repository\ReactionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReactionsRepository::class)
 */
class Reactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=BlogUser::class)
     */
    private $blogUser;

    /**
     * @ORM\ManyToOne(targetEntity=BlogPost::class)
     */
    private $blogPost;

    /**
     * @ORM\Column(type="boolean")
     */
    private $has_reaction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlogUser(): ?BlogUser
    {
        return $this->blogUser;
    }

    public function setBlogUser(?BlogUser $blogUser): self
    {
        $this->blogUser = $blogUser;

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    public function getHasReaction(): ?bool
    {
        return $this->has_reaction;
    }

    public function setHasReaction(bool $has_reaction): self
    {
        $this->has_reaction = $has_reaction;

        return $this;
    }
}
