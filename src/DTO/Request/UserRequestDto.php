<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email(message: 'Почта не валидна')]
        private string $email,
        #[Assert\NotBlank]
        private string $password,
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
