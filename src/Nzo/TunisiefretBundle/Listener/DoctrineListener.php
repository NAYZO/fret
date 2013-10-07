<?php

namespace Nzo\TunisiefretBundle\Listener; 

use Doctrine\ORM\Event\LifecycleEventArgs;
use Nzo\UserBundle\Entity\Client;
use Nzo\UserBundle\Entity\Exportateur;
use Nzo\TunisiefretBundle\Entity\Notification;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;

class DoctrineListener
{
    protected $mailer, $templating;
    
    public function __construct($mailer, TimedTwigEngine $templating) 
    {
         $this->mailer = $mailer;
         $this->templating = $templating;
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Client) {
            $message = 'Nouveau Client Inscrit';
        }
        else if ($entity instanceof Exportateur) {
            $message = 'Nouveau Affréteur Inscrit';
        }
        
            $notif = new Notification();
            $admin = $em->getRepository('NzoUserBundle:Admin')->find(10);
            $notif->setAdmin($admin);
            $notif->setText($message);
            $em->persist($notif);
            $em->flush();
            
        $envoi = \Swift_Message::newInstance()
                ->setSubject('Notification Tunisie Fret')
                ->setFrom('tunisiefret@gmail.com')
                ->setTo($admin->getEmail())
                ->setBody($this->templating->render('NzoTunisiefretBundle:Front:NotifEmail.html.twig', array('message' => $message)));
        $this->mailer->send($envoi);
    }
}