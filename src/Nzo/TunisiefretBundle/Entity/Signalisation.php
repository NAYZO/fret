<?php

namespace Nzo\TunisiefretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="tunisiefret_signalisation")
 * @ORM\Entity(repositoryClass="Nzo\TunisiefretBundle\Entity\Repository\SignalisationRepository")
 */
class Signalisation
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
     * @ORM\ManyToOne(targetEntity="Nzo\TunisiefretBundle\Entity\DemandeExport")
     */
    private $demandeexport;
    
    /**
     * @ORM\ManyToOne(targetEntity="Nzo\UserBundle\Entity\Client")
     */
    private $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="Nzo\UserBundle\Entity\Exportateur")
     */
    private $exportateur;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\Column(name="titre", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $titre;
    
    /**
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $type;
    
    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    protected $description;
    
    
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
     * Set date
     *
     * @param \DateTime $date
     * @return Signialisation
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
     * Set titre
     *
     * @param string $titre
     * @return Signialisation
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Signialisation
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Signialisation
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set demandeexportpostule
     *
     * @param \Nzo\TunisiefretBundle\Entity\DemandeExportPostule $demandeexportpostule
     * @return Signialisation
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
     * Set demandeexport
     *
     * @param \Nzo\TunisiefretBundle\Entity\DemandeExport $demandeexport
     * @return Signialisation
     */
    public function setDemandeexport(\Nzo\TunisiefretBundle\Entity\DemandeExport $demandeexport = null)
    {
        $this->demandeexport = $demandeexport;
    
        return $this;
    }

    /**
     * Get demandeexport
     *
     * @return \Nzo\TunisiefretBundle\Entity\DemandeExport 
     */
    public function getDemandeexport()
    {
        return $this->demandeexport;
    }

    /**
     * Set client
     *
     * @param \Nzo\UserBundle\Entity\Client $client
     * @return Signialisation
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
     * @return Signialisation
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