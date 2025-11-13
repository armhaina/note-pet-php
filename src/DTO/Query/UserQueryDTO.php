<?php

declare(strict_types=1);

namespace App\DTO\Query;

readonly class UserQueryDTO
{
    public function __construct(
        private ?int $limit = null,
        private ?int $offset = null,
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

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }
}
