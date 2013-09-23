<?php

namespace Nzo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PUGX\MultiUserBundle\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="tunisiefret_exportateur")
 * @UniqueEntity(fields = "username", targetClass = "Nzo\UserBundle\Entity\User", message="fos_user.username.already_used")
 * @UniqueEntity(fields = "email", targetClass = "Nzo\UserBundle\Entity\User", message="fos_user.email.already_used")
 * @Vich\Uploadable
 */
class Exportateur extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="Nzo\TunisiefretBundle\Entity\DemandeExportPostule", mappedBy="exportateur", cascade={"remove"})
     */
    private $demandeexportpostule;
    
    /**
     * @ORM\OneToMany(targetEntity="Nzo\TunisiefretBundle\Entity\Notification", mappedBy="exportateur", cascade={"remove"})
     */
    private $notification;
    
    /**
     * @ORM\OneToMany(targetEntity="Nzo\TunisiefretBundle\Entity\NotifMsg", mappedBy="exportateur", cascade={"remove"})
     */
    private $notifmsg;
    
    /**
     * @var string $nomentrop
     *
     * @ORM\Column(name="nomentrop", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $nomentrop;
    
    /**
     * @var string $ville
     *
     * @ORM\Column(name="ville", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $ville;
    
    /**
     * @var string $adresse
     *
     * @ORM\Column(name="adresse", type="text")
     * @Assert\NotBlank()
     */
    protected $adresse;
    
    /**
     * @var string $tel
     *
     * @ORM\Column(name="tel", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $tel;
    
    /**
     * @var string $fax
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    protected $fax;
    
    /**
     * @var string $siteweb
     *
     * @ORM\Column(name="siteweb", type="string", length=255, nullable=true)
     */
    protected $siteweb;
    
    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
    /**
     * @var string $note
     *
     * @ORM\Column(name="note", type="float")
     * @Assert\NotBlank()
     */
    protected $note;
    
    /**
     * @var datetime $dateinscription
     *
     * @ORM\Column(name="dateinscription", type="datetime")
     */
    protected $dateinscription;
    
    /**
     * @var datetime $dateenabled
     *
     * @ORM\Column(name="dateenabled", type="datetime", nullable=true)
     */
    protected $dateenabled;
    
    /**
     * @var integer $nbdemandeexportpostuler
     *
     * @ORM\Column(name="nbdemandeexportpostule", type="integer")
     */
    protected $nbdemandeexportpostule;
    
    /**
     * @ORM\Column(name="nbcontratencours", type="integer", nullable=true)
     */
    protected $nbcontratencours;
    
    /**
     * @ORM\Column(name="nbcontrattermine", type="integer", nullable=true)
     */
    protected $nbcontrattermine;
    
    
    /**
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"},
     *     mimeTypesMessage = "SVP envoyez une image de type(jpg, jpeg ou png)")
     * @Vich\UploadableField(mapping="upload_logo_exportateur", fileNameProperty="logoname")
     *
     * @var File $uploadlogo
     */
    protected $uploadlogo;

    /**
     * @ORM\Column(type="string", length=255, name="logo", nullable=true)
     * 
     */
    protected $logoname; 
    
    /**
     * @ORM\Column(name="updatedvichat", type="datetime", nullable=true)
     */
    protected $UpdatedVichAt;
 

    public function __construct()
    {
      
        parent::__construct();
        $this->addRole('ROLE_EXPORTATEUR');
        
        $this->note = -1;
        $this->dateinscription = new \DateTime('now');
        $this->notifier = 0;
        $this->notifiermsg = 0;
        //$this->recevoiremail = true;
        $this->nbdemandeexportpostule = 0;
        $this->nbcontratencours = 0;   
        $this->nbcontrattermine = 0;
        $this->logoname = 'default-user-icon-profile.png';
        $this->demandeexportpostule = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notification = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notifmsg = new \Doctrine\Common\Collections\ArrayCollection();
    }

    
    /*****************************************************************************/  
    public function getUploadlogo() {
        return $this->uploadlogo;
    }

    public function setUploadlogo(File $uploadlogo = null) {
        $this->uploadlogo = $uploadlogo;
        
        if ($uploadlogo instanceof File) {
            $this->setUpdatedVichAt(new \DateTime());
        }
    }
  /*******************************************************************************/

   

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
     * Set nomentrop
     *
     * @param string $nomentrop
     * @return Exportateur
     */
    public function setNomentrop($nomentrop)
    {
        $this->nomentrop = $nomentrop;
    
        return $this;
    }

    /**
     * Get nomentrop
     *
     * @return string 
     */
    public function getNomentrop()
    {
        return $this->nomentrop;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Exportateur
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
     * Set adresse
     *
     * @param string $adresse
     * @return Exportateur
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
     * Set tel
     *
     * @param string $tel
     * @return Exportateur
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    
        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return Exportateur
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    
        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set siteweb
     *
     * @param string $siteweb
     * @return Exportateur
     */
    public function setSiteweb($siteweb)
    {
        $this->siteweb = $siteweb;
    
        return $this;
    }

    /**
     * Get siteweb
     *
     * @return string 
     */
    public function getSiteweb()
    {
        return $this->siteweb;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Exportateur
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
     * Set note
     *
     * @param float $note
     * @return Exportateur
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return float 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set dateinscription
     *
     * @param \DateTime $dateinscription
     * @return Exportateur
     */
    public function setDateinscription($dateinscription)
    {
        $this->dateinscription = $dateinscription;
    
        return $this;
    }

    /**
     * Get dateinscription
     *
     * @return \DateTime 
     */
    public function getDateinscription()
    {
        return $this->dateinscription;
    }

    /**
     * Set dateenabled
     *
     * @param \DateTime $dateenabled
     * @return Exportateur
     */
    public function setDateenabled($dateenabled)
    {
        $this->dateenabled = $dateenabled;
    
        return $this;
    }

    /**
     * Get dateenabled
     *
     * @return \DateTime 
     */
    public function getDateenabled()
    {
        return $this->dateenabled;
    }

    /**
     * Set nbdemandeexportpostule
     *
     * @param integer $nbdemandeexportpostule
     * @return Exportateur
     */
    public function setNbdemandeexportpostule($nbdemandeexportpostule)
    {
        $this->nbdemandeexportpostule = $nbdemandeexportpostule;
    
        return $this;
    }

    /**
     * Get nbdemandeexportpostule
     *
     * @return integer 
     */
    public function getNbdemandeexportpostule()
    {
        return $this->nbdemandeexportpostule;
    }

    /**
     * Set nbcontratencours
     *
     * @param integer $nbcontratencours
     * @return Exportateur
     */
    public function setNbcontratencours($nbcontratencours)
    {
        $this->nbcontratencours = $nbcontratencours;
    
        return $this;
    }

    /**
     * Get nbcontratencours
     *
     * @return integer 
     */
    public function getNbcontratencours()
    {
        return $this->nbcontratencours;
    }

    /**
     * Set nbcontrattermine
     *
     * @param integer $nbcontrattermine
     * @return Exportateur
     */
    public function setNbcontrattermine($nbcontrattermine)
    {
        $this->nbcontrattermine = $nbcontrattermine;
    
        return $this;
    }

    /**
     * Get nbcontrattermine
     *
     * @return integer 
     */
    public function getNbcontrattermine()
    {
        return $this->nbcontrattermine;
    }

    /**
     * Set logoname
     *
     * @param string $logoname
     * @return Exportateur
     */
    public function setLogoname($logoname)
    {
        $this->logoname = $logoname;
    
        return $this;
    }

    /**
     * Get logoname
     *
     * @return string 
     */
    public function getLogoname()
    {
        return $this->logoname;
    }

    /**
     * Set UpdatedVichAt
     *
     * @param \DateTime $updatedVichAt
     * @return Exportateur
     */
    public function setUpdatedVichAt($updatedVichAt)
    {
        $this->UpdatedVichAt = $updatedVichAt;
    
        return $this;
    }

    /**
     * Get UpdatedVichAt
     *
     * @return \DateTime 
     */
    public function getUpdatedVichAt()
    {
        return $this->UpdatedVichAt;
    }

    /**
     * Add demandeexportpostule
     *
     * @param \Nzo\TunisiefretBundle\Entity\DemandeExportPostule $demandeexportpostule
     * @return Exportateur
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
     * Add notification
     *
     * @param \Nzo\TunisiefretBundle\Entity\Notification $notification
     * @return Exportateur
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

    /**
     * Add notifmsg
     *
     * @param \Nzo\TunisiefretBundle\Entity\NotifMsg $notifmsg
     * @return Exportateur
     */
    public function addNotifmsg(\Nzo\TunisiefretBundle\Entity\NotifMsg $notifmsg)
    {
        $this->notifmsg[] = $notifmsg;
    
        return $this;
    }

    /**
     * Remove notifmsg
     *
     * @param \Nzo\TunisiefretBundle\Entity\NotifMsg $notifmsg
     */
    public function removeNotifmsg(\Nzo\TunisiefretBundle\Entity\NotifMsg $notifmsg)
    {
        $this->notifmsg->removeElement($notifmsg);
    }

    /**
     * Get notifmsg
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifmsg()
    {
        return $this->notifmsg;
    }
}