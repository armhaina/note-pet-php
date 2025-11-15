<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\Group;
use App\Enum\Role;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Model\Payload\UserPayloadModel;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    /**
     * @throws EntityNotFoundException
     */
    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_GET]
    )]
    public function get(int $id): JsonResponse
    {
        $entity = $this->userService->get(id: $id);

        return $this->json(data: $entity, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     */
    #[Route(methods: [Request::METHOD_POST])]
    public function create(#[MapRequestPayload] UserPayloadModel $model): JsonResponse
    {
        $entity = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($entity, $model->getPassword());

        $entity = (new User())
            ->setEmail(email: $model->getEmail())
            ->setPassword(password: $hashedPassword)
            ->setRoles(roles: [Role::ROLE_USER->value])
        ;

        $entity = $this->userService->create(entity: $entity);

        return $this->json(data: $entity, context: ['groups' => [Group::PUBLIC->value]]);
    }
}
