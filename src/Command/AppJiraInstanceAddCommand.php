<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;

use App\Jira\InstanceBuilder;

class AppJiraInstanceAddCommand extends Command
{
    protected static $defaultName = 'app:jira:instance:add';

    protected $builder;

    public function __construct(InstanceBuilder $builder)
    {
        $this->builder = $builder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add new instance of JIRA')
            ->addOption('name', false, InputOption::VALUE_OPTIONAL, 'Name of instance')
            ->addOption('baseUrl', false, InputOption::VALUE_OPTIONAL, 'URL of JIRA Instance')
            ->addOption('username', false, InputOption::VALUE_OPTIONAL, 'Username of JIRA Instance')
            ->addOption('token', false, InputOption::VALUE_OPTIONAL, 'Secret token')
            ->addOption('dryRun', false, InputOption::VALUE_NONE, 'Only show')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        
        $name     = $input->getOption('name');
        $baseUrl  = $input->getOption('baseUrl');
        $username = $input->getOption('username');
        $token    = $input->getOption('token');
        $dryRun   = $input->getOption('dryRun');
        
        $data = ['name' => $name, 'baseUrl' => $baseUrl, 'token' => $token, 'username' => $username];
        
        foreach ($data as $key=>$value) {
            if (!($value)) {
                $io->note(sprintf('You passed an argument: %s', $key));

                $question = new Question('Please enter '.$key.': ', 'demo');
                $data[$key] = $helper->ask($input, $output, $question);
            }
        }
        
        $io->note('Starting adding the instance');
        
        $instance = $this->builder->build($data['name'], $data['baseUrl'], $data['username'], $data['token']);
        if (is_null($instance)) {
            $io->error('The data you have provided are not correct. Cannot connect to JIRA.');
        } else {
            $io->success('JIRA instance has been successfully added with ID='.$instance->getId());
        }
    }
}
