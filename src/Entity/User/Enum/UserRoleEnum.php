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

namespace App\Entity\User\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;

class UserRoleEnum extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const USER = 'ROLE_USER';

    public const ADMIN = 'ROLE_ADMIN';
}
