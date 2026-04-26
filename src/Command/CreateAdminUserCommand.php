<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Create the initial admin user')]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Admin email', 'admin@learnway.com');
        $plainPassword = $io->askHidden('Admin password (hidden)');

        if ($this->userRepository->findOneBy(['email' => $email])) {
            $io->error("A user with email «{$email}» already exists.");
            return Command::FAILURE;
        }

        $adminRole = $this->roleRepository->findOneBy(['name' => 'ADMIN']);
        if (!$adminRole) {
            $io->error('ADMIN role not found in the database.');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName('Admin');
        $user->setLastName('User');
        $user->setRole($adminRole);
        $user->setIsActive(true);
        $user->setCreatedAt(new \DateTime());
        $user->setPasswordHash($this->hasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);
        $this->em->flush();

        $io->success("Admin user «{$email}» created successfully.");
        return Command::SUCCESS;
    }
}
