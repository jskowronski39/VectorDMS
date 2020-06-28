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

namespace App\Service\Security\UserPassword;

use App\Entity\User\AbstractUser;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserPasswordService
{
    protected PasswordEncoderInterface $encoder;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoder = $encoderFactory->getEncoder(AbstractUser::class);
    }

    public function encodeUserPassword(string $passwordPlain): string
    {
        return $this->encoder->encodePassword($passwordPlain, null);
    }

    public function generateRandomPassword(int $length = 8): string
    {
        $generator = new ComputerPasswordGenerator();
        $generator
            ->setUppercase()
            ->setLowercase()
            ->setNumbers()
            ->setSymbols(false)
            ->setLength($length)
        ;

        return $generator->generatePassword();
    }
}
