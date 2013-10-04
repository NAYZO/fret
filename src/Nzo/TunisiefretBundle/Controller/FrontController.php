<?php

namespace Nzo\TunisiefretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;

class FrontController extends Controller
{    
    public function indexAction() 
    {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) 
        {
            return $this->render('NzoTunisiefretBundle:Admin:index.html.twig');
        } 
        else if ($this->get('security.context')->isGranted('ROLE_CLIENT')) 
        {  
            $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $active = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientDemandeExportActive($usr->getId());
            $nbarchive = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientNbDemandeExportArchive($usr->getId());
            $encours = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientContratEncoursTrois($usr->getId());
            return $this->render('NzoTunisiefretBundle:Client:index.html.twig', array('depose' => $active->execute(), 'nbarchive' => $nbarchive, 'encours' => $encours)); 
        }
        else if ($this->get('security.context')->isGranted('ROLE_EXPORTATEUR')) 
        {            
            return $this->redirect($this->generateUrl('exp_home'));
        }        
        else
            return $this->render('NzoTunisiefretBundle:Front:index.html.twig');
    }
    
    public function inscriptionAction ()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
             return $this->render('NzoTunisiefretBundle:Front:inscription.html.twig');
        }
        return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
    }
    
    public function contactAction(Request $request)
    {   
        if ($request->isXmlHttpRequest()) 
        {
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');
            $sujet = $request->request->get('sujet');
            $message = $request->request->get('message');
            $envoi = \Swift_Message::newInstance()
                ->setSubject('Contact Tunisie Fret')
                ->setFrom($email)
                ->setTo('tunisiefret@gmail.com')
                ->setBody($this->renderView('NzoTunisiefretBundle:Front:contactEmail.html.twig', array('nom' => $nom, 'email' => $email, 'sujet' => $sujet, 'message' => $message)), 'text/html');
            $this->get('mailer')->send($envoi);
            return new Response('Merci. votre message a été envoyé avec succès.');
        }    
    }
}
