<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Contract\EntityQueryModelInterface;

class NoteQueryModel implements EntityQueryModelInterface
{
    private ?int $limit = null;
    private ?int $offset = null;
    private ?array $ids = null;
    private array $orderBy = [];

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getIds(): ?array
    {
        return $this->ids;
    }

    public function setIds(array $ids): self
    {
        $this->ids = $ids;

        return $this;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function setOrderBy(array $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }
}
