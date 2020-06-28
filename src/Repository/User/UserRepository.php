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

namespace App\Repository\User;

use App\Entity\User\User;
use App\Entity\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method null|UserInterface find($id, $lockMode = null, $lockVersion = null)
 * @method null|UserInterface findOneBy(array $criteria, array $orderBy = null)
 * @method UserInterface[]    findAll()
 * @method UserInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUsername(string $username): ?UserInterface
    {
        return $this->findOneBy([
            'username' => $username,
        ]);
    }

    public function findOneByEmail(string $email): ?UserInterface
    {
        return $this->findOneBy([
            'email' => $email,
        ]);
    }
}
