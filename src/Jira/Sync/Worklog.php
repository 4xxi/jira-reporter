<?php

namespace App\Jira\Sync;

use App\Entity\Jira\Instance;

class Worklog
{
    
    /**
     * @var App\Entity\Jira\Instance
     */
    private $client;
    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $worklogs = [];
    
    /**
     * @var \App\Jira\Mapper\Worklog $worklogMapper
     */
    private $worklogMapper;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    /**
     * @var Instance
     */
    private $instance;

    /**
     * @param \App\Entity\Jira\Instance $client
     * @param \App\Jira\Mapper\Worklog $worklogMapper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\App\Jira\Client $client, 
                                \App\Jira\Mapper\Worklog $worklogMapper, 
                                \Doctrine\ORM\EntityManager $em, 
                                \Psr\Log\LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->em     = $em;
        $this->worklogMapper = $worklogMapper;
    }

    /**
     * @param App\Entity\Jira\Instance $instance
     * @param \DateTime or null $startDate
     * @param \DateTime or null $endDate
     */
    public function sync(Instance $instance, $startDate = false, $endDate = false)
    {
        $this->instance = $instance;

        if (is_string($startDate)) {
            $startDate = new \DateTime($startDate);
        }
        if (is_string($endDate)) {
            $endDate = new \DateTime($endDate);
        }
        
        $this->logger->info('Connecting to JIRA to get worklogs');
        
        $worklogs = $this->getWorklogsByPeriod($startDate, $endDate);
        foreach ($worklogs as $worklog) {
            $this->em->persist($worklog);
        }
        $this->logger->info(sprintf('Adding/Updating %d worklogs to the database', count($worklogs)));
        $this->em->flush();
        
        return $worklogs;
    }
    
    /**
     * @param \DateTime or null $startDate
     * @param \DateTime or null $endDate
     */
    private function getWorklogsByPeriod($startDate, $endDate)
    {
        $workLogs = [];

        $since = $startDate->getTimestamp() * 1000;
        $until = $endDate->getTimestamp() * 1000;

        do {
            $this->logger->info(sprintf('Making request with since=%s', $since));
            $response = $this->client->request($this->instance, '/rest/api/2/worklog/updated', ['since'  => $since]);
            
            /**
             * Hack if we have already exceed the limits
             */
            if ((isset($response->until)) && ((int)$response->until > $until)) {
                $this->logger->info(sprintf('We have reached end date: initial until=%s, current = %s', $until, $response->until));
                $response->lastPage = true;
            }
            
            /**
             * If it is not last page then we replace $since variable
             */
            if ($response->lastPage === false) {
                $nextPage = parse_url($response->nextPage);
                parse_str($nextPage['query'], $tmp); // update $since
                $since = $tmp['since'];
                $this->logger->info(sprintf('Starting next page with since=%s', $since));
            } else {
                $this->logger->info('We have reached the last page');
            }

            if (isset($response->values)) {
                $this->addWorklogs($response->values);
            }
        } while ($response->lastPage === false);
        return $this->worklogs;
    }

    /**
     * @param array $values
     * @return void
     */
    private function addWorklogs($values)
    {
        $ids = [];
        foreach ($values as $value) {
            $ids[] = $value->worklogId;
        }
        $logs = $this->getWorklogsByIds($ids);
        $obj = [];
        foreach ($logs as $log) {
            $obj[] = $this->worklogMapper->map($log);
        }
        $this->worklogs = array_merge($this->worklogs, $obj);
    }

    /**
     * @param array $ids
     * @return array|Api\Result|false
     */
    private function getWorklogsByIds($ids)
    {
        $this->logger->info('Getting worklogs by IDs');
        $response = $this->client->post($this->instance, '/rest/api/2/worklog/list', ['ids'  => $ids]);
        $this->logger->info(sprintf('Get %d worklogs by IDs', count($response)));
        return $response;
    }

}
