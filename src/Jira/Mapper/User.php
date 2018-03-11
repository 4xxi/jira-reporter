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
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
        
        $user->setJiraId($jiraUser->accountId);
        $user->setUsername($jiraUser->name);
        $user->setName($jiraUser->displayName);
        $user->setEmail($jiraUser->emailAddress);
        $user->setAvatarUrl($jiraUser->avatarUrls->$av);
        
        return $user;
    }
    
}