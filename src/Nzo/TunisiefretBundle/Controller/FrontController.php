<?php

namespace Nzo\TunisiefretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserInterface;

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
            return $this->render('NzoTunisiefretBundle:Client:index.html.twig', array('depose' => $active->execute(), 'nbarchive' => $nbarchive, 'encours' => $encours )); 
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
    
    public function contactAction()
    {
        
    }
}
