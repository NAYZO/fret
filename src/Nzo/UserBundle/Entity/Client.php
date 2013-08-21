<?php

namespace Nzo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PUGX\MultiUserBundle\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="tunisiefret_client")
 * @UniqueEntity(fields = "username", targetClass = "Nzo\UserBundle\Entity\User", message="fos_user.username.already_used")
 * @UniqueEntity(fields = "email", targetClass = "Nzo\UserBundle\Entity\User", message="fos_user.email.already_used")
 * @Vich\Uploadable
 */
class Client extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="Nzo\TunisiefretBundle\Entity\DemandeExport", mappedBy="client", cascade={"remove"})
     */
    private $demandeexport;
    
    /**
     * @ORM\OneToMany(targetEntity="Nzo\TunisiefretBundle\Entity\Notification", mappedBy="client", cascade={"remove"})
     */
    private $notification;
    
    /**
     * @ORM\OneToMany(targetEntity="Nzo\TunisiefretBundle\Entity\NotifMsg", mappedBy="client", cascade={"remove"})
     */
    private $notifmsg;
    
    /**
     * @var string $nom
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "50",
     *      minMessage = "Votre nom doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre nom ne peut pas être plus long que {{ limit }} caractères"
     * )
     */
    protected $nom;

    /**
     * @var string $prenom
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "50",
     *      minMessage = "Votre prénom doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre prénom ne peut pas être plus long que {{ limit }} caractères"
     * )
     */
    protected $prenom;
    
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
     * @var string $note
     *
     * @ORM\Column(name="note", type="float")
     * @Assert\NotBlank()
     */
    protected $note;
    
    /**
     * @var date $dateinscription
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
     * @var integer $nbdemandeexportdepose
     *
     * @ORM\Column(name="nbdemandeexportdepose", type="integer", nullable=true)
     */
    protected $nbdemandeexportdepose;
        
    
    /**
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"},
     *     mimeTypesMessage = "SVP envoyez une image de type(jpg, jpeg ou png)")
     * @Vich\UploadableField(mapping="upload_logo_client", fileNameProperty="logoname")
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
     * @ORM\Column(name="updatedvichat", type="datetime")
     */
    protected $UpdatedVichAt;
    

    public function __construct()
    {      
        parent::__construct();
        $this->addRole('ROLE_CLIENT');
        
        $this->note = 0;
        $this->dateinscription = new \DateTime('now');
        $this->notifier = 0;
        $this->notifiermsg = 0;
        //$this->recevoiremail = true;
        $this->nbdemandeexportdepose = 0;   
        $this->demandeexport = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     * @return Client
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Client
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    
        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Client
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
     * @return Client
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
     * @return Client
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
     * Set note
     *
     * @param float $note
     * @return Client
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
     * @return Client
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
     * @return Client
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
     * Set nbdemandeexportdepose
     *
     * @param integer $nbdemandeexportdepose
     * @return Client
     */
    public function setNbdemandeexportdepose($nbdemandeexportdepose)
    {
        $this->nbdemandeexportdepose = $nbdemandeexportdepose;
    
        return $this;
    }

    /**
     * Get nbdemandeexportdepose
     *
     * @return integer 
     */
    public function getNbdemandeexportdepose()
    {
        return $this->nbdemandeexportdepose;
    }

    /**
     * Set logoname
     *
     * @param string $logoname
     * @return Client
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
     * Add demandeexport
     *
     * @param \Nzo\TunisiefretBundle\Entity\DemandeExport $demandeexport
     * @return Client
     */
    public function addDemandeexport(\Nzo\TunisiefretBundle\Entity\DemandeExport $demandeexport)
    {
        $this->demandeexport[] = $demandeexport;
    
        return $this;
    }

    /**
     * Remove demandeexport
     *
     * @param \Nzo\TunisiefretBundle\Entity\DemandeExport $demandeexport
     */
    public function removeDemandeexport(\Nzo\TunisiefretBundle\Entity\DemandeExport $demandeexport)
    {
        $this->demandeexport->removeElement($demandeexport);
    }

    /**
     * Get demandeexport
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDemandeexport()
    {
        return $this->demandeexport;
    }
    

    /**
     * Add notification
     *
     * @param \Nzo\TunisiefretBundle\Entity\Notification $notification
     * @return Client
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
     * @return Client
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

    /**
     * Set UpdatedVichAt
     *
     * @param \DateTime $updatedVichAt
     * @return Client
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
}