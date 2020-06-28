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

namespace App\Entity\User\Traits;

/**
 * The only purpose of this trait is to remove blank methods required by Symfony UserInterface from user entity.
 */
trait SymfonyUserInterfaceTrait
{
    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // Do nothing
    }
}
