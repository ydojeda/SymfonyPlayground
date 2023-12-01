<?php

namespace App\DTO;

class BlogPostListEnquiry implements \JsonSerializable
{
    private ?int $userId;

    private ?int $limit = 50;

    private ?int $offset = 0;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}