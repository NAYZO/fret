<?php

namespace Nzo\TunisiefretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="tunisiefret_demande_export")
 * @ORM\Entity(repositoryClass="Nzo\TunisiefretBundle\Entity\Repository\DemandeExportRepository")
 */
class DemandeExport
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Nzo\UserBundle\Entity\Client")
     */
    private $client;
    
    /**
     * @ORM\OneToMany(targetEntity="Nzo\TunisiefretBundle\Entity\DemandeExportPostule", mappedBy="demandeexport", cascade={"remove"})
     */
    private $demandeexportpostule;
    
    /**
     * @ORM\Column(name="reference", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $reference;
    
    /**
     * @var string $titre
     *
     * @ORM\Column(name="titre", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $titre;
    
    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
    /**
     * @var string $pays
     *
     * @ORM\Column(name="pays", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $pays;
    
    /**
     * @var string $ville
     *
     * @ORM\Column(name="ville", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $ville;
    
    /**
     * @var integer $codepostal
     *
     * @ORM\Column(name="codepostal", type="integer")
     * @Assert\NotBlank()
     */
    protected $codepostal;
    
    /**
     * @var string $adresse
     *
     * @ORM\Column(name="adresse", type="text")
     * @Assert\NotBlank()
     */
    protected $adresse;
    
    /**
     * @var datetime $datemax
     *
     * @ORM\Column(name="datemax", type="datetime")
     * @Assert\NotBlank()
     */
    protected $datemax;
    
    /**
     * @var datetime $date_depos
     *
     * @ORM\Column(name="date_depos", type="datetime")
     * @Assert\NotBlank()
     */
    private $date_depos;

    /**
     * @ORM\Column(name="prix", type="float", nullable=true)
     */
    private $prix;
    
    /**
     * @var boolean $tacking
     *
     * @ORM\Column(name="tacking", type="boolean")
     */
    private $tacking;
    
    /**
     * @var boolean $jobend
     *
     * @ORM\Column(name="jobend", type="boolean")
     */
    private $jobend;
    
    /**
     * @ORM\Column(name="annuler", type="boolean")
     */
    private $annuler;
    
    /**
     * @var integer $nombredepostule
     *
     * @ORM\Column(name="nombredepostule", type="integer")
     */
    private $nombredepostule;
    
    /**
     * @var boolean $demandeexporttype
     *
     * @ORM\Column(name="demande_export_type", type="boolean", nullable=true)
     */
    protected $demandeexporttype;
    
    
    public function __construct()
    {
        $this->tacking = false; 
        $this->jobend = false; 
        $this->annuler = false; 
        $this->date_depos = new \DateTime('now');
        $this->nombredepostule = 0;
        $this->demandeexportpostule = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reference = uniqid($this->getId());
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
     * Set reference
     *
     * @param string $reference
     * @return DemandeExport
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    
        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return DemandeExport
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
     * Set description
     *
     * @param string $description
     * @return DemandeExport
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
     * Set pays
     *
     * @param string $pays
     * @return DemandeExport
     */
    public function setPays($pays)
    {
        $this->pays = $pays;
    
        return $this;
    }

    /**
     * Get pays
     *
     * @return string 
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return DemandeExport
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
    
        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set codepostal
     *
     * @param integer $codepostal
     * @return DemandeExport
     */
    public function setCodepostal($codepostal)
    {
        $this->codepostal = $codepostal;
    
        return $this;
    }

    /**
     * Get codepostal
     *
     * @return integer 
     */
    public function getCodepostal()
    {
        return $this->codepostal;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return DemandeExport
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    
        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set datemax
     *
     * @param \DateTime $datemax
     * @return DemandeExport
     */
    public function setDatemax($datemax)
    {
        $this->datemax = $datemax;
    
        return $this;
    }

    /**
     * Get datemax
     *
     * @return \DateTime 
     */
    public function getDatemax()
    {
        return $this->datemax;
    }

    /**
     * Set date_depos
     *
     * @param \DateTime $dateDepos
     * @return DemandeExport
     */
    public function setDateDepos($dateDepos)
    {
        $this->date_depos = $dateDepos;
    
        return $this;
    }

    /**
     * Get date_depos
     *
     * @return \DateTime 
     */
    public function getDateDepos()
    {
        return $this->date_depos;
    }

    /**
     * Set prix
     *
     * @param float $prix
     * @return DemandeExport
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;
    
        return $this;
    }

    /**
     * Get prix
     *
     * @return float 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set tacking
     *
     * @param boolean $tacking
     * @return DemandeExport
     */
    public function setTacking($tacking)
    {
        $this->tacking = $tacking;
    
        return $this;
    }

    /**
     * Get tacking
     *
     * @return boolean 
     */
    public function getTacking()
    {
        return $this->tacking;
    }

    /**
     * Set jobend
     *
     * @param boolean $jobend
     * @return DemandeExport
     */
    public function setJobend($jobend)
    {
        $this->jobend = $jobend;
    
        return $this;
    }

    /**
     * Get jobend
     *
     * @return boolean 
     */
    public function getJobend()
    {
        return $this->jobend;
    }

    /**
     * Set nombredepostule
     *
     * @param integer $nombredepostule
     * @return DemandeExport
     */
    public function setNombredepostule($nombredepostule)
    {
        $this->nombredepostule = $nombredepostule;
    
        return $this;
    }

    /**
     * Get nombredepostule
     *
     * @return integer 
     */
    public function getNombredepostule()
    {
        return $this->nombredepostule;
    }
   
    /**
     * Set demandeexporttype
     *
     * @param boolean $demandeexporttype
     * @return DemandeExport
     */
    public function setDemandeexporttype($demandeexporttype)
    {
        $this->demandeexporttype = $demandeexporttype;
    
        return $this;
    }

    /**
     * Get demandeexporttype
     *
     * @return boolean 
     */
    public function getDemandeexporttype()
    {
        return $this->demandeexporttype;
    }

    /**
     * Set client
     *
     * @param \Nzo\UserBundle\Entity\Client $client
     * @return DemandeExport
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
     * Add demandeexportpostule
     *
     * @param \Nzo\TunisiefretBundle\Entity\DemandeExportPostule $demandeexportpostule
     * @return DemandeExport
     */
    public function addDemandeexportpostule(\Nzo\TunisiefretBundle\Entity\DemandeExportPostule $demandeexportpostule)
    {
        $this->demandeexportpostule[] = $demandeexportpostule;
    
        return $this;
    }

    /**
     * Remove demandeexportpostule
     *
     * @param \Nzo\TunisiefretBundle\Entity\DemandeExportPostule $demandeexportpostule
     */
    public function removeDemandeexportpostule(\Nzo\TunisiefretBundle\Entity\DemandeExportPostule $demandeexportpostule)
    {
        $this->demandeexportpostule->removeElement($demandeexportpostule);
    }

    /**
     * Get demandeexportpostule
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDemandeexportpostule()
    {
        return $this->demandeexportpostule;
    }

    /**
     * Set annuler
     *
     * @param boolean $annuler
     * @return DemandeExport
     */
    public function setAnnuler($annuler)
    {
        $this->annuler = $annuler;
    
        return $this;
    }

    /**
     * Get annuler
     *
     * @return boolean 
     */
    public function getAnnuler()
    {
        return $this->annuler;
    }
}