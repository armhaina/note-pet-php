<?php

declare(strict_types=1);

namespace App\DTO\Query;

readonly class NoteQueryDTO
{
    public function __construct(
        private ?int $limit = null,
        private ?int $offset = null,
        private ?int $user = null,
        private array $orderBy = [],
    ) {}

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }
}
