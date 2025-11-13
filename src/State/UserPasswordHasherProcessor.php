<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserPasswordHasherProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @param User $data
     *
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data->getPlainPassword()) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            $data->getPlainPassword()
        );
        $data->setPassword($hashedPassword);
        $data->eraseCredentials();

        // Если это операция по созданию нового пользователя, то генерим токе и назначаем роль по умолчанию
        if ($operation instanceof Post) {
            $data->setToken(bin2hex(random_bytes(60)));
            $data->setRoles($data->getRoles());
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
