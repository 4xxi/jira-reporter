<?php

namespace App\Jira;

use App\Entity\Jira\Instance;

class InstanceBuilder
{
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \App\Jira\Client
     */
    private $client;
    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \App\Jira\Client $client
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, \App\Jira\Client $client, Psr\Log\LoggerInterface $logger)
    {
        $this->em     = $em;
        $this->client = $client;
        $this->logger = $logger;
    }
    
    /**
     * @param string $name
     * @param string $baseUrl
     * @param string $username
     * @param string $token
     */
    public function build($name, $baseUrl, $username, $token)
    {
        $instance = new Instance();
        $instance->setName($name);
        $instance->setBaseUrl($baseUrl);
        $instance->setUsername($username);
        $instance->setToken($token);
        
        if ($this->client->checkConnection($instance)) {
            $this->em->persist($instance);
            $this->em->flush();
            return $instance;
        }
        return null;
    }
    
    
}