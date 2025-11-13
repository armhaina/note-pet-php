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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityInvalidObjectTypeException
     * @throws ExceptionInterface
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

        $content = $this->serializer->serialize(data: $entity, format: 'json', context: ['groups' => ['public']]);

        return new JsonResponse(data: $content, json: true);
    }
}
