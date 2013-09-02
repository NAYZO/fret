<?php

namespace Nzo\TunisiefretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="tunisiefret_annuler_demande_export")
 * @ORM\Entity(repositoryClass="Nzo\TunisiefretBundle\Entity\Repository\AnnulerDemandeExportRepository")
 */
class AnnulerDemandeExport
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="raison", type="string", length=255, nullable=true)
     */
    protected $raison;
    
    /**
     * @var datetime $date_annuler
     *
     * @ORM\Column(name="date_annuler", type="datetime")
     */
    private $date_annuler;
    
    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
    public function __construct()
    {
        $this->date_annuler = new \DateTime('now');
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
     * Set date_annuler
     *
     * @param \DateTime $dateAnnuler
     * @return AnnulerDemandeExport
     */
    public function setDateAnnuler($dateAnnuler)
    {
        $this->date_annuler = $dateAnnuler;
    
        return $this;
    }

    /**
     * Get date_annuler
     *
     * @return \DateTime 
     */
    public function getDateAnnuler()
    {
        return $this->date_annuler;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AnnulerDemandeExport
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
     * Set raison
     *
     * @param string $raison
     * @return AnnulerDemandeExport
     */
    public function setRaison($raison)
    {
        $this->raison = $raison;
    
        return $this;
    }

    /**
     * Get raison
     *
     * @return string 
     */
    public function getRaison()
    {
        return $this->raison;
    }
}