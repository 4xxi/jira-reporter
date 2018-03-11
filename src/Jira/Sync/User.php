<?php

namespace App\Jira\Sync;

use App\Entity\Jira\Instance;

class User
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
    private $users = [];
    
    /**
     * @var \App\Jira\Mapper\User $userMapper
     */
    private $userMapper;
    
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
     * @param \App\Jira\Mapper\User $userMapper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\App\Jira\Client $client, 
                                \App\Jira\Mapper\User $userMapper, 
                                \Doctrine\ORM\EntityManager $em, 
                                \Psr\Log\LoggerInterface $logger)
    {
        $this->client     = $client;
        $this->logger     = $logger;
        $this->em         = $em;
        $this->userMapper = $userMapper;
    }

    /**
     * @param App\Entity\Jira\Instance $instance
     * @param string|null $group
     */
    public function sync(Instance $instance, $group)
    {
        $this->instance = $instance;

        $this->logger->info('Connecting to JIRA to get worklogs');
        
        if ($group) {
            $users = $this->getUsersByGroup($group);
        } else {
            $users = $this->getUsers();
        }

        foreach ($users as $user) {
            $this->em->persist($user);
        }
        $this->logger->info(sprintf('Adding/Updating %d worklogs to the database', count($users)));
        $this->em->flush();
        
        return $users;
    }
    
    /**
     * @param string $group
     * @return array
     */
    private function getUsersByGroup($group)
    {
        $this->logger->info(sprintf('Getting users from group %s from JIRA', $group));
        $response = $this->client->request($this->instance, '/rest/api/2/group/member', ['groupname' => $group, 'maxResults' => 1000]);

        if (isset($response->values)) {
            $this->logger->info(sprintf('Processing response. %d users found', count($response->values)));
            foreach ($response->values as $jiraUser) {
                $this->users[] = $this->userMapper->map($jiraUser);
            }
        }
        return $this->users;
    }
    
    /**
     * @return array
     */
    private function getUsers()
    {
        $this->logger->info(sprintf('Getting users from JIRA'));
        $response = $this->client->request($this->instance, '/rest/api/2/user/search', ['username' => '%', 'maxResults' => 1000]);

        if (isset($response)) {
            $this->logger->info(sprintf('Processing response. %d users found', count($response)));
            foreach ($response as $jiraUser) {
                $this->users[] = $this->userMapper->map($jiraUser);
            }
        }
        return $this->users;
    }

}
