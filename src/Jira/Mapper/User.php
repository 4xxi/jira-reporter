<?php
    
namespace App\Jira\Mapper;

use App\Entity\Jira\User as EntityUser;
use App\Repository\Jira\UserRepository;

class User {
    
    /**
     * @var WorklogRepository
     */
    private $userRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository, \Psr\Log\LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }
    
    /**
     * @param array $jiraWorklog
     * @return \App\Entity\Jira\User
     */
    public function map($jiraUser)
    {
        $user = $this->userRepository->findByUsername($jiraUser->name);
        if (!$user) {
            $user = new EntityUser();
            $user->setActive(true);
        }
        
        $av = '48x48';
        try {
            $user->setJiraId($jiraUser->key);
            $user->setUsername($jiraUser->name);
            $user->setName($jiraUser->displayName);
            $user->setEmail($jiraUser->emailAddress);
            $user->setAvatarUrl($jiraUser->avatarUrls->$av);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->logger->info(serialize($jiraUser));
            return null;
        }
        
        return $user;
    }
    
}