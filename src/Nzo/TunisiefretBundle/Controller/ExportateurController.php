<?php

namespace Nzo\TunisiefretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Nzo\TunisiefretBundle\Form\DemandeExportType;
use Nzo\TunisiefretBundle\Entity\DemandeExport;
use Nzo\TunisiefretBundle\Entity\DemandeExportPostule;

use Nzo\TunisiefretBundle\Entity\MsgDemandeExport;

use Nzo\TunisiefretBundle\Entity\NotifMsg;
use Nzo\TunisiefretBundle\Entity\Notification;
use Nzo\TunisiefretBundle\Entity\AnnulerDemandeExport;
use Nzo\TunisiefretBundle\Entity\TerminerDemandeExport;
use Nzo\TunisiefretBundle\Entity\AvisExport;

class ExportateurController extends Controller {
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function PostuleDemandeExportAction(DemandeExport $DemandeExport, Request $request)
    {
        // test if posted before
     //    if($this->GetEtatAction($DemandeExport) == 'true') return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
        // test if posted before
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $PostuleExport = new DemandeExportPostule();
        $PostuleExport->setExportateur($usr);
        $PostuleExport->setDemandeexport($DemandeExport);
        $form = $this->createForm(new DemandeExportPostuleType(), $PostuleExport);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {                
                //augmente NB postule sur la Demande
                $DemandeExport->setNombredepostule($DemandeExport->getNombredepostule()+1);
                //Notification Client
                $Notification = new Notification();
                $Notification->setClient($DemandeExport->getClient());
                $Notification->setText('Nouveau Postule sur la Demande <span>'.$DemandeExport->getTitre().'</span>');
                // end Notification
                //augmente NB postule de l'Exportateur
                $usr->setNbdemandeexportpostule($usr->getNbdemandeexportpostule()+1);   
                $em->persist($Notification);
                $em->persist($PostuleExport);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'c boooonn!');
                return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
            }
        }
        $res = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->findBy( array('exportateur' => $usr, 'demandeexport' => $DemandeExport));
        ($res != NULL)? $val='true' : $val='false';
        return $this->render('NzoTunisiefretBundle:Exportateur:PostuleDemandeExport.html.twig', array('val' => $val, 'demandeexport' =>$DemandeExport, 'form' => $form->createView()));
    }
    
    /**
     * @Secure(roles="ROLE_EXPORTATEUR")
     */
    public function AjaxGetNbNotifAction(Request $request) 
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $nbnotifs = $em->getRepository('NzoTunisiefretBundle:Notification')->getNbNotifExportateur($usr);
        if ($request->isXmlHttpRequest()) 
        return new Response($nbnotifs);
        return new Response($nbnotifs);
    }
    
    /**
     * @Secure(roles="ROLE_EXPORTATEUR")
     */
    public function AjaxGetNbMsgAction(Request $request) 
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $nbmsgs = $em->getRepository('NzoTunisiefretBundle:NotifMsg')->getNbMsgExportateur($usr);
        if ($request->isXmlHttpRequest()) 
        return new Response($nbmsgs);
        return new Response($nbmsgs);
    }
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function GetEtatAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $res = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->findBy( array('exportateur' => $usr, 'demandeexport' => $id));
        ($res != NULL)? $val='true' : $val='false';
        return new Response($val);
    }
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ListePostuleActiveAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.tacking = 0 AND d.annuler_demande is NULL AND a.demande_refuser = 0 AND a.annuler_by_exportateur = 0 AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
        $paginator = $this->get('knp_paginator'); 
        $listepostules = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Exportateur:ListePostuleActive.html.twig', array('listepostules' => $listepostules));
    }
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ListePostuleArchiveAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.tacking = 0 OR d.annuler_demande is NOT NULL OR a.demande_refuser = 1 OR a.annuler_by_exportateur = 1 AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
        $paginator = $this->get('knp_paginator'); 
        $listepostules = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Exportateur:ListePostuleArchive.html.twig', array('listepostules' => $listepostules));
    }
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ListeTravailEnCoursAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.tacking = 1 AND d.terminer_demande is NULL AND d.annuler_demande is NULL AND a.demande_refuser = 0 AND a.annuler_by_exportateur = 0 AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
        $paginator = $this->get('knp_paginator'); 
        $listepostules = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Exportateur:ListeTravailEnCours.html.twig', array('listepostules' => $listepostules));
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ListeTravailTermineAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.terminer_demande is NOT NULL AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
        $paginator = $this->get('knp_paginator'); 
        $listepostules = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Exportateur:ListeTravailTermine.html.twig', array('listepostules' => $listepostules));
    }
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function DetailPostuleActiveAction(DemandeExportPostule $postule)
    {
       // security access     
            if($postule->getDemandeexport()->getTacking() || $postule->getDemandeexport()->getAnnulerDemande() || $postule->getAnnulerByExportateur() || $postule->getDemandeRefuser()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
     
//        $etat='';
//        if ($postule->getDemandeexport()->getTerminerDemande())
//            $etat = 'terminer';
//        else if ($postule->getDemandeexport()->getAnnulerDemande())
//            $etat = 'annuler_par_client';
//        else if ($postule->getDemandeexport()->getTacking() && !$postule->getDemandeexport()->getTerminerDemande() )
            
            
        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
   
        return $this->render('NzoTunisiefretBundle:Exportateur:DetailPostuleActive.html.twig', array('postule' => $postule, 'msgs' => $msgs));    
    }
    
//============================================================================================================================================================= nn terminer
    public function EtatExport(DemandeExportPostule $postule)
    {
        $etat='';
        if ($postule->getDemandeexport()->getTerminerDemande())
            $etat = 'terminer';
        else if ($postule->getDemandeexport()->getAnnulerDemande())
            $etat = 'annuler_par_client';
        else if ($postule->getDemandeexport()->getTacking() && !$postule->getDemandeexport()->getTerminerDemande() )
            
       return $etat;
    }

    /**
     * @Secure(roles="ROLE_EXPORTATEUR")
     */
    public function AjaxGetNotifAction(Request $request) 
    {
        if ($request->isXmlHttpRequest()) {  
            
            $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
        
            $notifs = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifAjaxExportateur($usr);
            if($notifs != NULL){
                $i = 0;
                foreach ($notifs as $res) {
                    $notifdate = $res->getDate()->format('d/m/Y H:i');
                    $vu = $res->getVu();
                    $val[$i] = array('date' => $notifdate, 'notiftext' => $res->getText(), 'notifvu' => $vu);
                    // set Vu to True
                    if(!$vu){
                    $res->setVu(true);
                    $em->persist($res);           
                    }
                    $i++;
                }
                $em->flush();
            }
            else
                $val = array('vide');
        return new Response(json_encode($val));
        }
    }  
    
    /**
     * @Secure(roles="ROLE_EXPORTATEUR")
     */
    public function MessageExportateurSendAction(Request $request) 
    {
        if ($request->isXmlHttpRequest()) {
            // save msg
            $msg = $request->request->get('msg');
            $id = $request->request->get('id');                    
            $em = $this->getDoctrine()->getManager();
            $usr = $this->get('security.context')->getToken()->getUser();
            $MsgForm = new MsgDemandeExport();    
            $MsgForm->setExportateur($usr);                
            $postule = $em->find('NzoTunisiefretBundle:DemandeExportPostule', $id);
            $MsgForm->setDemandeexportpostule($postule);
            $MsgForm->setMessage($msg);
                $em->persist($MsgForm);
                $em->flush();
            // notif Client
            $client = $postule->getDemandeexport()->getClient();
            $notifmsg = new NotifMsg();
            $notifmsg->setClient($client);
            $notifmsg->setEmetteur($usr->getNomentrop());
            $notifmsg->setLogoemetteur($usr->getLogoname());
            $notifmsg->setTitredemandeexport($postule->getDemandeexport()->getTitre());
            $notifmsg->setText($msg);           
            $em->persist($notifmsg);
            $em->flush();

           //  recuperation liste msg
           $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));

           $i = 0;
            foreach ($msgs as $res) {
                $msgdate = $res->getDate()->format('d/m/Y H:i');
                if($usr==$res->getExportateur() ) $msguser='Moi'; else $msguser=$postule->getDemandeexport()->getClient()->getNomentrop();
                $val[$i] = array('date' =>$msgdate, 'msg' => $res->getMessage(), 'user' => $msguser);
                $i++;
            }
        return new Response(json_encode($val));
        }
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function AnnulerPostuleAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getExportateur() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access
            $em = $this->getDoctrine()->getManager();
            $postule->setAnnulerByExportateur(true);

            // notif Client
                $notif = new Notification();
                $notif->setClient($postule->getDemandeexport()->getClient());
                //==================================================================================================================== lien exportateur pour info demande export + classe css
                $text = $postule->getExportateur()->getNomentrop().'a annulé son postule pour la Demande <span>'.$postule->getDemandeexport()->getTitre().'</span>';
                $notif->setText($text);
                $em->persist($notif);
            
            $em->flush();
        return $this->redirect($this->generateUrl('exp_liste_postule_active'));   
  }
} 
