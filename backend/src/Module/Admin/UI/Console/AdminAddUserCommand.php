<?php

declare(strict_types=1);

namespace App\Module\Admin\UI\Console;

use App\Module\Admin\Entity\UserAdmin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'admin:add_user',
    description: 'Добавление пользователя админки',
)]
class AdminAddUserCommand extends Command
{
    public function __construct(
        protected UserPasswordHasherInterface $passwordHasher,
        protected EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');

        $helper = $this->getHelper('question');

        $question = new Question('Enter passphrase: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);


        $user = new UserAdmin();
        $user
            ->setUsername($username)
            ->setPassword($this->passwordHasher->hashPassword($user, $password))
            ->setEmail($username.'@mail.ru')
            ->setSalt(md5((new \DateTimeImmutable())->format('Y-m-d H:i:s s s i')))
            ->setRoles(['ROLE_ADMIN'])
        ;

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('User "%s" created successfully', $username));

        return Command::SUCCESS;

    }
}
