<?php declare(strict_types = 1);

namespace App\Command;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AppTestCommand
 */
class AppTestCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:test';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        $this->em = $entityManager;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Test')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $users = $this->em->getRepository(User::class)->findAll();

        $output->writeln('Start');
        $output->writeln('');
        foreach ($users as $user) {
            $output->writeln($user->getUsername());
        }
        $output->writeln('');
        $output->writeln('End');
    }
}
