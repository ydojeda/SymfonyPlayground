<?php

namespace App\DTO;

use http\Message\Body;

class BlogPostEnquiry
{
    private ?int $userId;

    private ?string $tags = null;

    private ?string $body = null;

    private ?int $reactions = null;

    private ?int $timestamp = null;

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(?int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): void
    {
        $this->tags = $tags;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getReactions(): ?int
    {
        return $this->reactions;
    }

    public function setReactions(?int $reactions): void
    {
        $this->reactions = $reactions;
    }


}