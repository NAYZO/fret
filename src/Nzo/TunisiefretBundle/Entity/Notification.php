<?php

namespace Nzo\TunisiefretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tunisiefret_notification")
 * @ORM\Entity(repositoryClass="Nzo\TunisiefretBundle\Entity\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Nzo\UserBundle\Entity\Client")
     */
    private $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="Nzo\UserBundle\Entity\Exportateur")
     */
    private $exportateur;
    
    /**
     * @ORM\ManyToOne(targetEntity="Nzo\UserBundle\Entity\Admin")
     */
    private $admin;

    /**
     * @ORM\Column(name="vu", type="boolean")
     */
    private $vu;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\Column(name="text", type="string", length=255, nullable=true)
     */
    private $text;
    
    /**
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;
    
    
    public function __construct()
    {
        $this->vu = false; 
        $this->date = new \DateTime('now');        
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
     * Set vu
     *
     * @param boolean $vu
     * @return Notification
     */
    public function setVu($vu)
    {
        $this->vu = $vu;
    
        return $this;
    }

    /**
     * Get vu
     *
     * @return boolean 
     */
    public function getVu()
    {
        return $this->vu;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Notification
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Notification
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Notification
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set client
     *
     * @param \Nzo\UserBundle\Entity\Client $client
     * @return Notification
     */
    public function setClient(\Nzo\UserBundle\Entity\Client $client = null)
    {
        $this->client = $client;
    
        return $this;
    }

    /**
     * Get client
     *
     * @return \Nzo\UserBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set exportateur
     *
     * @param \Nzo\UserBundle\Entity\Exportateur $exportateur
     * @return Notification
     */
    public function setExportateur(\Nzo\UserBundle\Entity\Exportateur $exportateur = null)
    {
        $this->exportateur = $exportateur;
    
        return $this;
    }

    /**
     * Get exportateur
     *
     * @return \Nzo\UserBundle\Entity\Exportateur 
     */
    public function getExportateur()
    {
        return $this->exportateur;
    }

    /**
     * Set admin
     *
     * @param \Nzo\UserBundle\Entity\Admin $admin
     * @return Notification
     */
    public function setAdmin(\Nzo\UserBundle\Entity\Admin $admin = null)
    {
        $this->admin = $admin;
    
        return $this;
    }

    /**
     * Get admin
     *
     * @return \Nzo\UserBundle\Entity\Admin 
     */
    public function getAdmin()
    {
        return $this->admin;
    }
}