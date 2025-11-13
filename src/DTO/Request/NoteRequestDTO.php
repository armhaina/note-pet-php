<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class NoteRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        private string $name,
        #[Assert\NotBlank]
        private string $description,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
