<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\UserRequestDTO;
use App\Entity\User;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityInvalidObjectTypeException
     */
    #[Route(methods: [Request::METHOD_POST])]
    public function create(#[MapRequestPayload] UserRequestDTO $requestDTO): JsonResponse
    {
        $entity = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($entity, $requestDTO->getPassword());

        $entity = (new User())
            ->setEmail(email: $requestDTO->getEmail())
            ->setPassword(password: $hashedPassword)
            ->setRoles(roles: [UserService::ROLE_USER])
        ;

        $entity = $this->userService->create(entity: $entity);

        return new JsonResponse(['success' => true]);
    }
}
