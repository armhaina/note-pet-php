<?php

declare(strict_types=1);

namespace App\DTO\Response;

readonly class UserResponseDTO
{
    public function __construct(
        public int $id,
        public int $telegram_id,
        public ?string $email,
    ) {}
}
