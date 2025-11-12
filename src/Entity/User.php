<?php

namespace App\Entity;

use App\Contract\EntityInterface;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\HasLifecycleCallbacks]
class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(
        name: 'roles',
        type: Types::JSON,
        nullable: false,
        options: ['comment' => 'Роли пользователя'],
    )]
    private array $roles;

    #[ORM\Column(
        name: 'email',
        type: Types::STRING,
        length: 255,
        nullable: true,
        options: ['comment' => 'Электронная почта пользователя'],
    )]
    private ?string $email = null;

    #[ORM\Column(
        name: 'password',
        type: Types::STRING,
        length: 255,
        nullable: true,
        options: ['comment' => 'Пароль пользователя'],
    )]
    private ?string $password = null;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        nullable: false,
        options: ['comment' => 'Дата создания'],
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(
        name: 'updated_at',
        type: Types::DATETIME_IMMUTABLE,
        nullable: false,
        options: ['comment' => 'Дата изменения'],
    )]
    private \DateTimeImmutable $updatedAt;

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.).
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
