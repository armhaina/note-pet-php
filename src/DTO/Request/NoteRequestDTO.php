<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

readonly class NoteRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        private string $name,
        #[Assert\NotBlank]
        private string $description,
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::BOOLEAN, message: "Значение '{{ value }}' должно быть булевым.")]
        private bool $isPrivate,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIsPrivate(): bool
    {
        return $this->isPrivate;
    }
}
