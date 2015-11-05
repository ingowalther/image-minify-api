<?php

namespace IngoWalther\ImageMinifyApi\Command;

use IngoWalther\ImageMinifyApi\Security\ApiKeyGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AddUserCommand extends Command
{
    /**
     * @var ApiKeyGenerator
     */
    private $apiKeyGenerator;

    /**
     * @param ApiKeyGenerator $apiKeyGenerator
     */
    public function setApiKeyGenerator($apiKeyGenerator)
    {
        $this->apiKeyGenerator = $apiKeyGenerator;
    }


    protected function configure()
    {
        $this
            ->setName('user:add')
            ->setDescription('Creates a new User/API-Key')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Username?'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!$this->apiKeyGenerator) {
            throw new \Exception('ApiKeyGenerator is not set!');
        }

        $name = $input->getArgument('name');

        if(!$name) {
            /** @var QuestionHelper $helper */
            $helper = $this->getHelper('question');
            $question = new Question('<question>Username?</question> ');

            $name = $helper->ask($input, $output, $question);
        }

        $key = $this->apiKeyGenerator->generate($name);

        $output->writeln(sprintf('<info>User "%s" succesfully created</info>', $name));
        $output->writeln(sprintf('<info>API-Key: %s</info>', $key));
    }

}