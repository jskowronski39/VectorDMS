<?php

/*
 * This file is part of the Vector DMS package.
 *
 * (c) Jakub Skowroński
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use App\Entity\User\Enum\UserRoleEnum;
use App\Entity\User\Traits\SymfonyUserInterfaceTrait;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

abstract class AbstractUser extends AbstractEntity implements SymfonyUserInterface
{
    use SymfonyUserInterfaceTrait;

    protected string $username;
    protected string $email;
    protected ?string $password;
    protected bool $admin = false;

    public function __construct(UuidInterface $id, string $username, string $email)
    {
        parent::__construct($id);

        $this->username = $username;
        $this->email = $email;
    }

    public function getRoles(): array
    {
        return $this->isAdmin() ? [UserRoleEnum::ADMIN] : [UserRoleEnum::USER];
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;
    }
}
