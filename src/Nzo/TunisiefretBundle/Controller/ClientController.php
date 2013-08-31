<?php

namespace Nzo\TunisiefretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Nzo\TunisiefretBundle\Form\DemandeExportType;
use Nzo\TunisiefretBundle\Entity\DemandeExport;
use Nzo\TunisiefretBundle\Entity\DemandeExportPostule;

use Nzo\TunisiefretBundle\Form\MsgDemandeExportType;
use Nzo\TunisiefretBundle\Entity\MsgDemandeExport;

use Nzo\TunisiefretBundle\Entity\NotifMsg;

class ClientController extends Controller {

   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function PoserDemandeExportAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $demande = new DemandeExport();
        $demande->setClient($usr);
        $form = $this->createForm(new DemandeExportType(), $demande);
        if ($this->getRequest()->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $usr->setNbdemandeexportdepose($usr->getNbdemandeexportdepose()+1);
                $em->persist($demande);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'c boooonn!');
                return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
            }
        }
        return $this->render('NzoTunisiefretBundle:Client:PoserDemandeExport.html.twig', array('form' => $form->createView()));
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function AnnulerDemandeExportAction(DemandeExport $mydemande)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
            $em = $this->getDoctrine()->getManager();
            $mydemande->setAnnuler(true);
            $em->persist($mydemande);
            $em->flush();
        return $this->redirect($this->generateUrl('nzo_voirlistedemande_export_active'));   
        
        // ============================================================================================================================================ manque notif exportateur
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function SupprimerDemandeExportTypeAction(DemandeExport $mydemande)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
            $em = $this->getDoctrine()->getManager();
            $mydemande->setDemandeexporttype(false);
            $em->persist($mydemande);
            $em->flush();
        return $this->redirect($this->generateUrl('client_list_demande_type'));    
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ReposterDemandeExportTypeAction(DemandeExport $mydemande, Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $demande = new DemandeExport();
            $demande->setAnnuler(false);
            $demande->setJobend(false);
            $demande->setTacking(false);
            $demande->setDemandeexporttype(false);
            $demande->setNombredepostule(0);
            $demande->setAdresse($mydemande->getAdresse());
            $demande->setClient($mydemande->getClient());
            $demande->setCodepostal($mydemande->getCodepostal());
            $demande->setDateDepos($mydemande->getDateDepos());
            $demande->setDatemax($mydemande->getDatemax());            
            $demande->setDescription($mydemande->getDescription());
            $demande->setPays($mydemande->getPays());
            $demande->setPrix($mydemande->getPrix());
            $demande->setReference($mydemande->getReference());
            $demande->setTitre($mydemande->getTitre());
            $demande->setVille($mydemande->getVille());
            
        $form = $this->createForm(new DemandeExportType(), $demande);
        if ($this->getRequest()->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                
                $em->persist($demande);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'c boooonn!');
                return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
            }
        }
        return $this->render('NzoTunisiefretBundle:Client:PoserDemandeExportType.html.twig', array('form' => $form->createView()));
    }
    
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ListeDemandeExportActiveAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientDemandeExportActive($usr->getId());
            $paginator = $this->get('knp_paginator'); 
            $listedemandeexport = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:ListeDemandeExportActive.html.twig', array('listedemandeexport' => $listedemandeexport));
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ListeContratEncoursAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientContratEncours($usr->getId());
            $paginator = $this->get('knp_paginator'); 
            $listecontratencours = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:ListeContratEncours.html.twig', array('listecontratencours' => $listecontratencours));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ListeContratTerminerAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientContratTerminer($usr->getId());
            $paginator = $this->get('knp_paginator'); 
            $listecontratterminer = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:ListeContratTerminer.html.twig', array('listecontratterminer' => $listecontratterminer));
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ListeDemandeExportTypeAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientDemandeExportType($usr->getId());
            $paginator = $this->get('knp_paginator'); 
            $listedemandeexporttype = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:ListeDemandeExportType.html.twig', array('listedemandeexporttype' => $listedemandeexporttype));
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ListeDemandeExportArchiveAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientDemandeExportArchive($usr->getId());
            $paginator = $this->get('knp_paginator'); 
            $listedemandeexport = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:ListeDemandeExportArchive.html.twig', array('listedemandeexport' => $listedemandeexport));
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DetailDemandeExportAction($id)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $mydemande = $em->find('NzoTunisiefretBundle:DemandeExport', $id);
       // security access     
            if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access   
            $postules = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->getDemandeExportPostuleByDemande($id);
        return $this->render('NzoTunisiefretBundle:Client:DetailDemandeExport.html.twig', array('mydemande' => $mydemande, 'postules' => $postules));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DetailDemandeExportTypeAction(DemandeExport $mydemande)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
       // security access     
            if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access   
            
        return $this->render('NzoTunisiefretBundle:Client:DetailDemandeExportType.html.twig', array('mydemande' => $mydemande));
    }

   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DetailDemandeExportPostuleAction(DemandeExportPostule $postule)
    { 
        $usr = $this->get('security.context')->getToken()->getUser();
        // security access 
        if($postule->getDemandeexport()->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
        // security access  
//        $MsgForm = new MsgDemandeExport();
//        $form = $this->createForm(new MsgDemandeExportType(), $MsgForm);

            $em = $this->getDoctrine()->getManager();        
            $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
        return $this->render('NzoTunisiefretBundle:Client:DetailDemandeExportPostule.html.twig', array('postule' => $postule, 'msgs' => $msgs));
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     */
    public function MessageClientSendAction(Request $request) 
    {
        if ($request->isXmlHttpRequest()) {
            // save msg
            $msg = $request->request->get('msg');
            $id = $request->request->get('id');                    
            $em = $this->getDoctrine()->getManager();
            $usr = $this->get('security.context')->getToken()->getUser();
            $MsgForm = new MsgDemandeExport();    
            $MsgForm->setClient($usr);                
            $postule = $em->find('NzoTunisiefretBundle:DemandeExportPostule', $id);
            $MsgForm->setDemandeexportpostule($postule);
            $MsgForm->setMessage($msg);
                $em->persist($MsgForm);
                $em->flush();
            // notif Exportateur
            $exportateur = $postule->getExportateur();
            $notifmsg = new NotifMsg();
            $notifmsg->setExportateur($exportateur);
            $notifmsg->setEmetteur($usr->getPrenom().' '.$usr->getNom());
            $notifmsg->setTitredemandeexport($postule->getDemandeexport()->getTitre());
            if (strlen($msg)> 70){
                $text = substr($msg, 0, 70).'...';
                $notifmsg->setText($text);
            }            
            else
                $notifmsg->setText($msg);
            $em->persist($notifmsg);
            $em->flush();

           //  recuperation liste msg
           $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));

           $i = 0;
            foreach ($msgs as $res) {
                $msgdate = $res->getDate()->format('d/m/Y H:i');
                if($usr==$res->getClient() ) $msguser='Moi'; else $msguser=$res->getExportateur()->getPrenom().' '.$res->getExportateur()->getNom();
                $val[$i] = array('date' =>$msgdate, 'msg' => $res->getMessage(), 'user' => $msguser);
                $i++;
            }
        return new Response(json_encode($val));
        }
    }
}
