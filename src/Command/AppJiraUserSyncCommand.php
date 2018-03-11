<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppJiraUserSyncCommand extends Command
{
    protected static $defaultName = 'app:jira:user:sync';

    /**
     * @var App\Repository\Jira\InstanceRepository $instanceRepository
     */
    protected $instanceRepository;
    /**
     * @var App\Jira\Sync\User $userSync
     */
    protected $userSync;

    public function __construct(\App\Repository\Jira\InstanceRepository $instanceRepository, 
                                \App\Jira\Sync\User $userSync)
    {
        $this->instanceRepository = $instanceRepository;
        $this->userSync           = $userSync;

        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Sync worklogs with JIRA Instance')
            ->addOption('instance', false, InputOption::VALUE_OPTIONAL, 'ID of JIRA instance')
            ->addOption('dry-run', false, InputOption::VALUE_NONE, 'Only show')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $instance_id = (int)$input->getOption('instance');
        $dryRun      = $input->getOption('dry-run');
        
        if (!$instance_id) {
            $io->error('Instance ID is required');
            return null;
        }
        
        $instance = $this->instanceRepository->find($instance_id);
        
        if (!$instance) {
            $io->error('Instance with ID='.$instance_id.' not found');
            return null;
        }

        $users = $this->userSync->sync($instance);
        $io->success(sprintf('Sync has been successfully completed! %d users have been updated!', count($users)));
    }
}
