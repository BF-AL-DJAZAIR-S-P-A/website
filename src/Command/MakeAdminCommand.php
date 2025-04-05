<?php

namespace App\Command;

use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:make-admin',
    description: 'Attribue le rôle ROLE_ADMIN à un utilisateur',
)]
class MakeAdminCommand extends Command
{
    private UsersRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UsersRepository $userRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $output->writeln("<error>Utilisateur avec l'email $email non trouvé.</error>");
            return Command::FAILURE;
        }

        $roles = $user->getRoles();

        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);
            $this->entityManager->flush();
            $output->writeln("<info>ROLE_ADMIN attribué à $email avec succès.</info>");
        } else {
            $output->writeln("<comment>L'utilisateur a déjà le rôle ROLE_ADMIN.</comment>");
        }

        return Command::SUCCESS;
    }
}