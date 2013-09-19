<?php

namespace Nzo\TunisiefretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tunisiefret_notifmsg")
 * @ORM\Entity(repositoryClass="Nzo\TunisiefretBundle\Entity\Repository\NotifMsgRepository")
 */
class NotifMsg
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
     * @ORM\Column(name="vu", type="boolean")
     */
    private $vu;
    
     /**
     * @ORM\Column(name="vumsg", type="boolean")
     */
    private $vumsg;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\Column(name="titre_demande_export", type="string", length=255, nullable=true)
     */
    private $titredemandeexport;
    
    /**
     * @ORM\Column(name="text", type="string", length=255, nullable=true)
     */
    private $text;
    
    /**
     * @ORM\Column(name="emetteur", type="string", length=255, nullable=true)
     */
    private $emetteur;
    
    /**
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;
    
    /**
     * @ORM\Column(name="logo_emetteur", type="string", length=255, nullable=true)
     */
    private $logoemetteur;
    
    
    public function __construct()
    {
        $this->vu = false; 
        $this->vumsg = false; 
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
     * @return NotifMsg
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
     * @return NotifMsg
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
     * Set titredemandeexport
     *
     * @param string $titredemandeexport
     * @return NotifMsg
     */
    public function setTitredemandeexport($titredemandeexport)
    {
        $this->titredemandeexport = $titredemandeexport;
    
        return $this;
    }

    /**
     * Get titredemandeexport
     *
     * @return string 
     */
    public function getTitredemandeexport()
    {
        return $this->titredemandeexport;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return NotifMsg
     */
    public function setText($text, $max=70)
    {
        if (strlen($text) > $max){
                $text = substr($text, 0, $max).'...';
            }            
   
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
     * Set emetteur
     *
     * @param string $emetteur
     * @return NotifMsg
     */
    public function setEmetteur($emetteur)
    {
        $this->emetteur = $emetteur;
    
        return $this;
    }

    /**
     * Get emetteur
     *
     * @return string 
     */
    public function getEmetteur()
    {
        return $this->emetteur;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return NotifMsg
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
     * @return NotifMsg
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
     * @return NotifMsg
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
     * Set logoemetteur
     *
     * @param string $logoemetteur
     * @return NotifMsg
     */
    public function setLogoemetteur($logoemetteur)
    {
        $this->logoemetteur = $logoemetteur;
    
        return $this;
    }

    /**
     * Get logoemetteur
     *
     * @return string 
     */
    public function getLogoemetteur()
    {
        return $this->logoemetteur;
    }

    /**
     * Set vumsg
     *
     * @param boolean $vumsg
     * @return NotifMsg
     */
    public function setVumsg($vumsg)
    {
        $this->vumsg = $vumsg;
    
        return $this;
    }

    /**
     * Get vumsg
     *
     * @return boolean 
     */
    public function getVumsg()
    {
        return $this->vumsg;
    }
}