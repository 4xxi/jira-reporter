<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Jira\User as User;

/**
 * @ORM\Table(name="offday")
 * @ORM\Entity(repositoryClass="App\Repository\OffdayRepository")
 */
class Offday
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Jira\User", inversedBy="jira_user", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;
    
    /**
     * @var \DateTime $startDate
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;
    
    /**
     * @var \DateTime $startDate
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;
    
    /**
     * @var boolean
     * @ORM\Column(name="off", type="boolean", nullable=true, options={"default": true})
     */
    private $off;
    
    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=30)
     */
    private $reason;
    
    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;
    
    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
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
     * @param User $user
     * @return Offday
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        
        return $this;
    }
    
    /**
     * @return User $user
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Offday
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Offday
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    /**
     * @param string $reason
     * @return Offday
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
    
    /**
     * @param bool $off
     *
     * @return Offday
     */
    public function setOff($off)
    {
        $this->off = $off;

        return $this;
    }

    /**
     * Get off
     *
     * @return bool
     */
    public function getOff()
    {
        return $this->off;
    }
    
    
    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

}
