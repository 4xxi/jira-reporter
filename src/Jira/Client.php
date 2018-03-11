<?php

namespace App\Jira;

class Client 
{
    
    private $curl;
    private $baseUrl;
    private $client;
    
    private $logger;
    
    public function __construct($logger)
    {
        $this->logger = $logger;
        
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_FAILONERROR, true);
    }
    
    private function setInstance($instance)
    {
        $this->baseUrl = $instance->getBaseUrl();
        curl_setopt($this->curl, CURLOPT_USERPWD, $instance->getUsername().":".$instance->getToken());
        return $this->curl;
    }
    
    private function setEndpoint($endpoint)
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl.$endpoint);
    }
    
    public function checkConnection($instance)
    {
        $this->setInstance($instance);
        $this->setEndpoint('/rest/api/2/project');
        $res = (curl_exec($this->curl));
        
        if (curl_error($this->curl)) {
            $this->logger->error('Error in JIRA connection: '.curl_error($this->curl));
            return false;
        } else {
            return true;
        }
    }
    
}
