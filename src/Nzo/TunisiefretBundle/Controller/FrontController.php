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
            return $this->render('NzoTunisiefretBundle:Client:index.html.twig');
        }
        else if ($this->get('security.context')->isGranted('ROLE_EXPORTATEUR')) 
        {            
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getAllDemandeExport();
            $paginator = $this->get('knp_paginator'); 
            $demandeexport = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Exportateur:index.html.twig', array('demandeexport' => $demandeexport));
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
