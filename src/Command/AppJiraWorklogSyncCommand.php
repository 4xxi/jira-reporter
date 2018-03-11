<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppJiraWorklogSyncCommand extends Command
{
    protected static $defaultName = 'app:jira:worklog:sync';
    
    /**
     * @var App\Repository\Jira\InstanceRepository $instanceRepository
     */
    protected $instanceRepository;
    /**
     * @var App\Jira\Sync\Worklog $worklogSync
     */
    protected $worklogSync;

    public function __construct(\App\Repository\Jira\InstanceRepository $instanceRepository, 
                                \App\Jira\Sync\Worklog $worklogSync)
    {
        $this->instanceRepository = $instanceRepository;
        $this->worklogSync        = $worklogSync;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Sync worklogs with JIRA Instance')
            ->addOption('startDate', false, InputOption::VALUE_OPTIONAL, 'Start writing logs in the interval', new \DateTime('-1 day'))
            ->addOption('endDate', false, InputOption::VALUE_OPTIONAL, 'End of the time interval', new \DateTime())
            ->addOption('instance', false, InputOption::VALUE_OPTIONAL, 'ID of JIRA instance')
            ->addOption('dry-run', false, InputOption::VALUE_NONE, 'Only show')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $startDate   = $input->getOption('startDate');
        $endDate     = $input->getOption('endDate');
        $instance_id = (int)$input->getOption('instance');
        $dryRun      = $input->getOption('dry-run');
        
        if (is_string($startDate)) {
            $startDate = new \DateTime($startDate);
        }
        if (is_string($endDate)) {
            $endDate = new \DateTime($endDate);
        }
        
        if (!$instance_id) {
            $io->error('Instance ID is required');
            return null;
        }
        
        $instance = $this->instanceRepository->find($instance_id);
        
        if (!$instance) {
            $io->error('Instance with ID='.$instance_id.' not found');
            return null;
        }
        
        $io->note('Starting sync process. Check logs for details.');
        $worklogs = $this->worklogSync->sync($instance, $startDate, $endDate);
        
        $io->success(sprintf('Sync has been successfully completed! %d worklogs have been updated!', count($worklogs)));
    }
}
