<?php

namespace App\Entity\Jira;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use App\Model\TimeTable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Jira\UserRepository")
 * @ORM\Table(name="jira_user")
 */
class User
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
     * @ORM\OneToMany(targetEntity="\App\Entity\Offday", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $offdays;

    /**
     * @var string
     *
     * @ORM\Column(name="jiraId", type="string", length=100)
     */
    private $jiraId;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="avatarUrl", type="string", length=255)
     */
    private $avatarUrl;
    
    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean", nullable=true, options={"default": true})
     */
    private $active;

    private $timeTable;
    
    public function setTimeTable($startDate, $endDate, $holidayDays)
    {
        $this->timeTable = new TimeTable();
        $this->timeTable->setInterval($startDate, $endDate);
        $this->timeTable->setOffDays($this->offdays);
        $this->timeTable->setHolidays($holidayDays);
    }
    
    public function addToTimeTable($date, $value)
    {
        if (!$this->timeTable) {
            throw new \Exception('TimeTable does not exist. It cannot be');
        }
        return $this->timeTable->add($date, $value);
    }
    
    public function getTimeTable()
    {
        return $this->timeTable;
    }
    
    public function __construct()
    {
        $this->offdays = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
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
     * Set avatarUrl
     *
     * @param string $avatarUrl
     *
     * @return User
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * Get avatarUrl
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }
    
    /**
     * Set active
     *
     * @param bool $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }
    
    /**
     * Add addOffday
     *
     * @param \App\Entity\Offday $offday
     *
     * @return User
     */
    public function addOffday(\App\Entity\Offday $offday)
    {
        if ($this->offdays->contains($offday)) {
            return;
        }
        $this->offdays[] = $offday;
        $offday->setUser($this);
    }

    /**
     * Remove offday
     *
     * @param \App\Entity\Offday $offday
     */
    public function removeOffday(\App\Entity\Offday $offday)
    {
        $this->offdays->removeElement($offday);
        $offday->setUser(null);
    }

    /**
     * Get offdays
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOffdays()
    {
        return $this->offdays;
    }

    public function __toString()
    {
        if ($this->getName()) {
            return $this->getName();
        }
        return $this->getUsername();
    }

}
