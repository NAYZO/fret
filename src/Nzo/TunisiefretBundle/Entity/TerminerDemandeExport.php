<?php

namespace Nzo\TunisiefretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="tunisiefret_terminer_demande_export")
 * @ORM\Entity(repositoryClass="Nzo\TunisiefretBundle\Entity\Repository\TerminerDemandeExportRepository")
 */
class TerminerDemandeExport
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var datetime $date_jobend
     *
     * @ORM\Column(name="date_jobend", type="datetime")
     */
    private $date_jobend;   
    
    /**
     * @var string $resultat
     *
     * @ORM\Column(name="resultat", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $resultat;
    
    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
    public function __construct()
    {
        $this->date_jobend = new \DateTime('now');
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
     * Set date_jobend
     *
     * @param \DateTime $dateJobend
     * @return TerminerDemandeExport
     */
    public function setDateJobend($dateJobend)
    {
        $this->date_jobend = $dateJobend;
    
        return $this;
    }

    /**
     * Get date_jobend
     *
     * @return \DateTime 
     */
    public function getDateJobend()
    {
        return $this->date_jobend;
    }

    /**
     * Set resultat
     *
     * @param string $resultat
     * @return TerminerDemandeExport
     */
    public function setResultat($resultat)
    {
        $this->resultat = $resultat;
    
        return $this;
    }

    /**
     * Get resultat
     *
     * @return string 
     */
    public function getResultat()
    {
        return $this->resultat;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return TerminerDemandeExport
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
}