<?php
    
namespace App\Jira\Mapper;

use App\Entity\Jira\Worklog as EntityWorklog;
use App\Repository\Jira\WorklogRepository;

class Worklog {
    
    /**
     * @var WorklogRepository
     */
    private $worklogRepository;
    
    /**
     * @param WorklogRepository $worklogRepository
     */
    public function __construct(WorklogRepository $worklogRepository)
    {
        $this->worklogRepository = $worklogRepository;
    }
    
    /**
     * @param array $jiraWorklog
     * @return \App\Entity\Jira\Worklog
     */
    public function map($jiraWorklog)
    {
        $worklog = $this->worklogRepository->findOneByJiraId($jiraWorklog->id);
        if (!$worklog) {
            $worklog = new EntityWorklog();
        }

        $worklog->setJiraId($jiraWorklog->id);
        $worklog->setIssueId($jiraWorklog->issueId);
        $worklog->setUsername($jiraWorklog->author->name);
        $worklog->setTimeSpent($jiraWorklog->timeSpentSeconds);
        $worklog->setCreated(new \DateTime($jiraWorklog->created));
        $worklog->setUpdated(new \DateTime($jiraWorklog->updated));
        
        return $worklog;
    }
    
}