<?php

namespace App\Command;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:make-admin',
    description: 'Crée un administrateur ou attribue le rôle ROLE_ADMIN à un utilisateur existant.',
)]
class MakeAdminCommand extends Command
{
    private UsersRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UsersRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('password', InputArgument::OPTIONAL, 'Mot de passe pour l\'utilisateur (uniquement si nouvel utilisateur)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            if (!$password) {
                $output->writeln("<error>Utilisateur non trouvé. Merci de fournir un mot de passe pour créer l'utilisateur.</error>");
                return Command::FAILURE;
            }
            // Création d'un nouvel utilisateur admin
            $user = new Users();
            $user->setEmail($email);
            $hashed = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashed);
            $user->setRoles(['ROLE_ADMIN']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $output->writeln("<info>Nouvel utilisateur admin créé avec succès : $email</info>");
            return Command::SUCCESS;
        }

        // Utilisateur existant, on ajoute le rôle admin si nécessaire
        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles, true)) {
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