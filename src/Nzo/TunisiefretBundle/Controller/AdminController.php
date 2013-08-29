<?php

namespace Nzo\TunisiefretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;


class AdminController extends Controller {

   /**
    * @Secure(roles="ROLE_ADMIN")
    */

    public function ListeClientsAction()
    {
        $em = $this->getDoctrine()->getManager();        
        $query = $em->createQuery("SELECT a FROM NzoUserBundle:Client a ORDER BY a.dateinscription DESC");
        $paginator = $this->get('knp_paginator'); 
        $listeclients = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Admin:ListeClients.html.twig', array('listeclients' => $listeclients));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */

    public function ListeExportateursAction()
    {
        $em = $this->getDoctrine()->getManager();        
        $query = $em->createQuery("SELECT a FROM NzoUserBundle:Exportateur a ORDER BY a.dateinscription DESC");
        $paginator = $this->get('knp_paginator'); 
        $listeexportateurs = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Admin:ListeExportateurs.html.twig', array('listeexportateurs' => $listeexportateurs));
    }
  
}
