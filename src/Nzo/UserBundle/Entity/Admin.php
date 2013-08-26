<?php

namespace Nzo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PUGX\MultiUserBundle\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="tunisiefret_admin")
 * @UniqueEntity(fields = "username", targetClass = "Nzo\UserBundle\Entity\User", message="fos_user.username.already_used")
 * @UniqueEntity(fields = "email", targetClass = "Nzo\UserBundle\Entity\User", message="fos_user.email.already_used")
 */
class Admin extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="Nzo\TunisiefretBundle\Entity\Notification", mappedBy="admin", cascade={"remove"})
     */
    private $notification;
    

    public function __construct()
    {      
        parent::__construct();
        $this->notification = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
   

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add notification
     *
     * @param \Nzo\TunisiefretBundle\Entity\Notification $notification
     * @return Admin
     */
    public function addNotification(\Nzo\TunisiefretBundle\Entity\Notification $notification)
    {
        $this->notification[] = $notification;
    
        return $this;
    }

    /**
     * Remove notification
     *
     * @param \Nzo\TunisiefretBundle\Entity\Notification $notification
     */
    public function removeNotification(\Nzo\TunisiefretBundle\Entity\Notification $notification)
    {
        $this->notification->removeElement($notification);
    }

    /**
     * Get notification
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotification()
    {
        return $this->notification;
    }
}