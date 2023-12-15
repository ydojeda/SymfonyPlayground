<?php

namespace App\DTO;

class BlogPostListEnquiry implements \JsonSerializable
{

    private ?int $limit = 50;

    private ?int $offset = 0;

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?string $limit): self
    {
        if ($limit !== null) {
            $validated_limit = filter_var($limit, FILTER_SANITIZE_NUMBER_INT);
            if ($validated_limit) {
                $this->limit = (int) $limit;
            }
        }

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?string $offset): self
    {
        if ($offset !== null) {
            $validated_limit = filter_var($offset, FILTER_SANITIZE_NUMBER_INT);
            $this->offset = (int) $offset;
        }

        return $this;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}