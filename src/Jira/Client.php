<?php

namespace App\Jira;

use App\Entity\Jira\Instance;

class Client 
{
    
    private $curl;
    private $baseUrl;
    private $client;
    
    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     * @param Psr\Log\LoggerInterface $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
        
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_FAILONERROR, true);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=UTF-8'));
    }
    
    /**
     * @param Instance $instant
     */
    private function setInstance(Instance $instance)
    {
        $this->baseUrl = $instance->getBaseUrl();
        curl_setopt($this->curl, CURLOPT_USERPWD, $instance->getUsername().":".$instance->getToken());
        return $this->curl;
    }
    
    private function setEndpoint($endpoint)
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl.$endpoint);
    }
    
    /**
     * @param Instance $instant
     */
    public function checkConnection(Instance $instance)
    {
        $this->setInstance($instance);
        $this->setEndpoint('/rest/api/2/project');

        curl_setopt($this->curl, CURLOPT_POST, 0);        

        $res = (curl_exec($this->curl));
        
        if (curl_error($this->curl)) {
            $this->logger->error('Error in JIRA connection: '.curl_error($this->curl));
            return false;
        }

        return true;
    }
    
    /**
     * @param Instance $instance
     * @param string $endpoint - e.g. /rest/api/2/project. Starts with /
     * @param array $options
     */
    public function request($instance, $endpoint, array $options = [])
    {
        $this->setInstance($instance);
        if (is_array($options) && count($options) > 0) {
            $this->setEndpoint($endpoint.'?'.http_build_query($options));
        } else {
            $this->setEndpoint($endpoint);
        }
        
        curl_setopt($this->curl, CURLOPT_POST, 0);        

        $res = (curl_exec($this->curl));
        
        if (curl_error($this->curl)) {
            $this->logger->error('Error in JIRA connection: '.curl_error($this->curl));
            return null;
        }
        
        return json_decode($res);
    }

    /**
     * @param Instance $instance
     * @param string $endpoint - e.g. /rest/api/2/project. Starts with /
     * @param array $options
     */
    public function post($instance, $endpoint, array $options)
    {
        $this->setInstance($instance);
        $this->setEndpoint($endpoint);

        curl_setopt($this->curl, CURLOPT_POST, 1);        
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($options));
        
        $res = (curl_exec($this->curl));
        
        if (curl_error($this->curl)) {
            $this->logger->error('Error in JIRA connection: '.curl_error($this->curl));
            return null;
        }
        
        return json_decode($res);
    }
}
