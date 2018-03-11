<?php

namespace App\Jira;

use App\Entity\Jira\Instance;

class InstanceBuilder
{
    
    private $em;
    private $client;
    private $logger;
    
    public function __construct($em, $client, $logger)
    {
        $this->em     = $em;
        $this->client = $client;
        $this->logger = $logger;
    }
    
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