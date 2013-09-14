<?php

namespace Nzo\TunisiefretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="tunisiefret_avis_export")
 * @ORM\Entity(repositoryClass="Nzo\TunisiefretBundle\Entity\Repository\AvisExportRepository")
 */
class AvisExport
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;   
    
    /** 
     * @ORM\Column(name="avis_client", type="text", nullable=true)
     */
    protected $avisclient;
    
    /** 
     * @ORM\Column(name="avis_exportateur", type="text", nullable=true)
     */
    protected $avisexportateur;   
    
    /**
     * @ORM\Column(name="note_client", type="float", nullable=true)
     */
    protected $noteclient;
    
    /**
     * @ORM\Column(name="note_exportateur", type="float", nullable=true)
     */
    protected $noteexportateur;

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
     * Set avisclient
     *
     * @param string $avisclient
     * @return AvisExport
     */
    public function setAvisclient($avisclient)
    {
        $this->avisclient = $avisclient;
    
        return $this;
    }

    /**
     * Get avisclient
     *
     * @return string 
     */
    public function getAvisclient()
    {
        return $this->avisclient;
    }

    /**
     * Set avisexportateur
     *
     * @param string $avisexportateur
     * @return AvisExport
     */
    public function setAvisexportateur($avisexportateur)
    {
        $this->avisexportateur = $avisexportateur;
    
        return $this;
    }

    /**
     * Get avisexportateur
     *
     * @return string 
     */
    public function getAvisexportateur()
    {
        return $this->avisexportateur;
    }

    /**
     * Set noteclient
     *
     * @param float $noteclient
     * @return AvisExport
     */
    public function setNoteclient($noteclient)
    {
        $this->noteclient = $noteclient;
    
        return $this;
    }

    /**
     * Get noteclient
     *
     * @return float 
     */
    public function getNoteclient()
    {
        return $this->noteclient;
    }

    /**
     * Set noteexportateur
     *
     * @param float $noteexportateur
     * @return AvisExport
     */
    public function setNoteexportateur($noteexportateur)
    {
        $this->noteexportateur = $noteexportateur;
    
        return $this;
    }

    /**
     * Get noteexportateur
     *
     * @return float 
     */
    public function getNoteexportateur()
    {
        return $this->noteexportateur;
    }
}