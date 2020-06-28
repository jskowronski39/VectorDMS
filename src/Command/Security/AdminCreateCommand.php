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

namespace App\Command\Security;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use App\Service\Security\UserPassword\UserPasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminCreateCommand extends Command
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepository;
    protected UserPasswordService $userPasswordService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordService $userPasswordService
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordService = $userPasswordService;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:security:admin:create')
            ->setDescription('Creates new admin account')
            ->addArgument('username', InputArgument::REQUIRED, 'Username (e-mail)')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        if ($this->userRepository->findOneByUsername($username)) {
            $io->error(\sprintf('User with username "%s" already exists!', $username));

            return 1;
        }

        $password = $input->getArgument('password');
        $randomPassword = null;
        if (!$password) {
            $randomPassword = $this->userPasswordService->generateRandomPassword();
        }

        $plainPassword = $password ?? $randomPassword;

        $admin = new User(Uuid::uuid4(), $username, $username);
        $admin->setPassword($this->userPasswordService->encodeUserPassword($plainPassword));

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->writeln('Admin account successfully created');
        if ($randomPassword) {
            $io->writeln('Generated password:');
            $io->writeln($randomPassword);
        }

        return 0;
    }
}
