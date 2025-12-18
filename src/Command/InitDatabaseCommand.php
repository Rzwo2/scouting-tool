<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:init-database',
    description: 'Creates the initial super admin user if missing'
)]
class InitDatabaseCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $_ENV['SUPERADMIN_EMAIL'] ?? null;
        $password = $_ENV['SUPERADMIN_PASSWORD'] ?? null;

        if (!$email || !$password) {
            $output->writeln('<error>Missing SUPERADMIN_EMAIL or SUPERADMIN_PASSWORD env variables.</error>');

            return Command::FAILURE;
        }

        if ($this->userRepository->count(['email' => $email])) {
            $output->writeln('Super admin already exists.');

            return Command::SUCCESS;
        }

        $user = new User();
        $user->setUsername('superadmin')
            ->setEmail($email)
            ->setRoles(['ROLE_SUPER_ADMIN']);

        $hashed = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashed);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Super admin created.');

        return Command::SUCCESS;
    }
}
