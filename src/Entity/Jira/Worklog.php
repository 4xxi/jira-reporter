<?php

namespace App\Entity\Jira;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Jira\WorklogRepository")
 */
class Worklog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="jiraId", type="integer")
     */
    private $jiraId;

    /**
     * @var int
     *
     * @ORM\Column(name="issueId", type="integer")
     */
    private $issueId;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100)
     */
    private $username;

    /**
     * @var int
     *
     * @ORM\Column(name="timeSpent", type="integer")
     */
    private $timeSpent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set jiraId
     *
     * @param integer $jiraId
     *
     * @return JiraWorklog
     */
    public function setJiraId($jiraId)
    {
        $this->jiraId = $jiraId;

        return $this;
    }

    /**
     * Get jiraId
     *
     * @return int
     */
    public function getJiraId()
    {
        return $this->jiraId;
    }

    /**
     * Set issueId
     *
     * @param integer $issueId
     *
     * @return Worklog
     */
    public function setIssueId($issueId)
    {
        $this->issueId = $issueId;

        return $this;
    }

    /**
     * Get issueId
     *
     * @return int
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * Set issueName
     *
     * @param string $issueName
     *
     * @return Worklog
     */
    public function setIssueName($issueName)
    {
        $this->issueName = $issueName;

        return $this;
    }

    /**
     * Get issueName
     *
     * @return string
     */
    public function getIssueName()
    {
        return $this->issueName;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Worklog
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set timeSpent
     *
     * @param integer $timeSpent
     *
     * @return Worklog
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    /**
     * Get timeSpent
     *
     * @return int
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Worklog
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Worklog
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

}
