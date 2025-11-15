<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\NoteRequestDTO;
use App\Entity\Note;
use App\Entity\User;
use App\Enum\Group;
use App\Enum\Role;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Service\NoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/notes')]
class NoteController extends AbstractController
{
    public function __construct(
        private readonly NoteService $noteService,
    ) {}

    /**
     * @throws EntityNotFoundException
     */
    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_GET]
    )]
    #[IsGranted(Role::ROLE_USER->value)]
    public function get(int $id, #[CurrentUser] User $user): JsonResponse
    {
        $entity = $this->noteService->get(id: $id);

        return $this->json(data: $entity, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     */
    #[Route(methods: [Request::METHOD_POST])]
    #[IsGranted(Role::ROLE_USER->value)]
    public function create(#[MapRequestPayload] NoteRequestDTO $requestDTO): JsonResponse
    {
        $entity = (new Note())
            ->setName(name: $requestDTO->getName())
            ->setDescription(description: $requestDTO->getDescription())
            ->setIsPrivate(isPrivate: $requestDTO->getIsPrivate())
        ;

        $entity = $this->noteService->create(entity: $entity);

        return $this->json(data: $entity, context: ['groups' => [Group::PUBLIC->value]]);
    }
}
