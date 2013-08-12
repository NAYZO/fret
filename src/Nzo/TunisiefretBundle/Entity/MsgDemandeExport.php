<?php

namespace Nzo\TunisiefretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="tunisiefret_msg_demande_export")
 * @ORM\Entity(repositoryClass="Nzo\TunisiefretBundle\Entity\Repository\MsgDemandeExportRepository")
 */
class MsgDemandeExport
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
     * @ORM\ManyToOne(targetEntity="Nzo\TunisiefretBundle\Entity\DemandeExportPostule")
     */
    private $demandeexportpostule;
    
    /**
     * @ORM\ManyToOne(targetEntity="Nzo\UserBundle\Entity\Client")
     */
    private $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="Nzo\UserBundle\Entity\Exportateur")
     */
    private $exportateur;
    
    /**
     * @var text $message
     *
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @ORM\Column(name="date_envoi", type="datetime")
     */
    private $date;

        public function __construct()
    {
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
     * Set message
     *
     * @param string $message
     * @return MsgDemandeExport
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return MsgDemandeExport
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
     * Set demandeexportpostule
     *
     * @param \Nzo\TunisiefretBundle\Entity\DemandeExportPostule $demandeexportpostule
     * @return MsgDemandeExport
     */
    public function setDemandeexportpostule(\Nzo\TunisiefretBundle\Entity\DemandeExportPostule $demandeexportpostule = null)
    {
        $this->demandeexportpostule = $demandeexportpostule;
    
        return $this;
    }

    /**
     * Get demandeexportpostule
     *
     * @return \Nzo\TunisiefretBundle\Entity\DemandeExportPostule 
     */
    public function getDemandeexportpostule()
    {
        return $this->demandeexportpostule;
    }

    /**
     * Set client
     *
     * @param \Nzo\UserBundle\Entity\Client $client
     * @return MsgDemandeExport
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
     * @return MsgDemandeExport
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
}