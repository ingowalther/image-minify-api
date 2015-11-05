<?php

namespace IngoWalther\ImageMinifyApi\Command;

use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{
    /**
     * @var string
     */
    private $configPath;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param string $configPath
     */
    public function setConfigPath($configPath)
    {
        $this->configPath = $configPath;
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function configure()
    {
        $this
            ->setName('image-minify-api:setup')
            ->setDescription('Setups the project');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dump = file_get_contents($this->configPath . '/db.sql');

        $statement = $this->connection->prepare($dump);
        $statement->execute();

        if ($statement->errorCode() != 0) {
            throw new \Exception('Error while creating Database');
        }

        $output->writeln('<info>Database created successfully</info>');
    }
}