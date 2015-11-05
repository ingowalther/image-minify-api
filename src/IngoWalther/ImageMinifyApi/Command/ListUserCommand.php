<?php

namespace IngoWalther\ImageMinifyApi\Command;

use IngoWalther\ImageMinifyApi\Database\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListUserCommand extends Command
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this
            ->setName('user:list')
            ->setDescription('Lists all user');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['ID', 'Username', 'API-Key'])
             ->addRows($this->userRepository->findAll());

        $table->render();
    }
}