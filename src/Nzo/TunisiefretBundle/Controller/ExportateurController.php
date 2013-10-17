<?php

namespace Nzo\TunisiefretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Nzo\TunisiefretBundle\Form\DemandeExportPostuleType;
use Nzo\TunisiefretBundle\Entity\DemandeExport;
use Nzo\TunisiefretBundle\Entity\DemandeExportPostule;

use Nzo\TunisiefretBundle\Entity\MsgDemandeExport;

use Nzo\TunisiefretBundle\Entity\NotifMsg;
use Nzo\TunisiefretBundle\Entity\Notification;
use Nzo\TunisiefretBundle\Entity\AvisExport;

class ExportateurController extends Controller {
    
    /**
     * @Secure(roles="ROLE_EXPORTATEUR")
     */
    public function preExecute()
    {
        if( $this->getRequest()->attributes->has('id') ){     
            $decrypted = $this->get('nzo_url_encryptor')->decrypt( $this->getRequest()->attributes->get('id') );
            $this->getRequest()->attributes->set('id', $decrypted);
        }
        elseif ($this->getRequest()->request->has('id')) {
            $decrypted = $this->get('nzo_url_encryptor')->decrypt( $this->getRequest()->request->get('id') );
            $this->getRequest()->request->set('id', $decrypted);
        }
    }
    
    /**
     * @Secure(roles="ROLE_EXPORTATEUR")
     */
    public function HomeAction(Request $request) 
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getAllDemandeExport();
            $paginator = $this->get('knp_paginator'); 
            $demandeexport = $paginator->paginate($query,
            $request->query->get('page', 1), 6);        
            $nbactive = $em->createQuery("SELECT COUNT(a) FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.tacking = 0 AND d.annuler_demande is NULL AND a.demande_refuser = 0 AND a.annuler_by_exportateur = 0 AND a.exportateur = ".$usr->getId());
            $nbarchive = $em->createQuery("SELECT COUNT(a) FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.tacking = 0 OR d.annuler_demande is NOT NULL OR a.demande_refuser = 1 OR a.annuler_by_exportateur = 1 AND a.exportateur = ".$usr->getId());
            return $this->render('NzoTunisiefretBundle:Exportateur:index.html.twig', array('demandeexport' => $demandeexport, 'nbactive' => $nbactive->getSingleScalarResult(), 'nbarchive' => $nbarchive->getSingleScalarResult() ));
    }
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function NotificationUrlValeurAction(DemandeExportPostule $postule)
    {
            if($postule->getDemandeexport()->getTerminerDemande())
            $url = $this->get('router')->generate('exp_contrat_termine_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));    
            else if($postule->getDemandeexport()->getTacking())
            $url = $this->get('router')->generate('exp_contrat_encours_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));    
            else if($postule->getDemandeexport()->getAnnulerDemande())
            $url = $this->get('router')->generate('exp_detail_postule_archive', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));    
            else    
            $url = $this->get('router')->generate('exp_detail_postule_active', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));    
            
            return $this->redirect($url);
     }
    
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
                $em->persist($PostuleExport);
                $em->flush();
                //Notification Client
                $Notification = new Notification();
                $Notification->setClient($DemandeExport->getClient());
                $Notification->setText('Nouveau Postule sur la Demande <span>'.$DemandeExport->getTitre().'</span>');
                $url = $this->get('router')->generate('client_notif_url_val', array('id' => $this->get('nzo_url_encryptor')->encrypt($PostuleExport->getId())), true);              
                $Notification->setUrl($url);
                
                // end Notification
                //augmente NB postule de l'Exportateur
                $usr->setNbdemandeexportpostule($usr->getNbdemandeexportpostule()+1);   
                $em->persist($Notification);   
                $em->flush();
                
                //Email   
                $textmail = 'Nouveau Postule sur la Demande <a href="'.$url.'"><span>'.$DemandeExport->getTitre().'</span></a>';
                $this->get('nzo.mailer')->NzoSendMail($DemandeExport->getClient()->getEmail(), 9, $DemandeExport->getClient()->getNomentrop(), $textmail );

                $this->get('session')->getFlashBag()->set('nzonotice', 'Postule enregistré avec succès');
                return $this->redirect($this->generateUrl('exp_detail_postule_active', array('id' => $this->get('nzo_url_encryptor')->encrypt($PostuleExport->getId()))));  
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
        $id = $this->get('nzo_url_encryptor')->decrypt( $id );
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $res = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->findBy( array('exportateur' => $usr, 'demandeexport' => $id));
        
        if($res != NULL){
            $val= $res[0]->getId();
        }
        else
            $val='false';
        return new Response($val);
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ListeMessagesAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:NotifMsg')->getListMessagesExportateur($usr);
            // set Vu to True
            foreach ($query->execute() as $res) {
                if(!$res->getVu()){
                $res->setVu(true);
                $em->persist($res);           
                }
            }
            $em->flush();
            
            $paginator = $this->get('knp_paginator'); 
            $listemessages = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 8);         
            return $this->render('NzoTunisiefretBundle:Exportateur:ListNotifMessages.html.twig', array('listemessages' => $listemessages));
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
    public function ListeContratEnCoursAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.tacking = 1 AND d.terminer_demande is NULL AND d.annuler_demande is NULL AND a.demande_refuser = 0 AND a.annuler_by_exportateur = 0 AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
        $paginator = $this->get('knp_paginator'); 
        $listepostules = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Exportateur:ListeContratEnCours.html.twig', array('listepostules' => $listepostules));
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ListeContratTermineAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.terminer_demande is NOT NULL AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
        $paginator = $this->get('knp_paginator'); 
        $listepostules = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Exportateur:ListeContratTermine.html.twig', array('listepostules' => $listepostules));
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ProfilClientAction($id)
    {
        $em = $this->getDoctrine()->getManager();   
        $client = $em->getRepository('NzoUserBundle:Client')->find($id);
        $demandes = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->findBy(array('client' => $id, 'tacking' => 1), array('date_tacking' => 'DESC'));
        $active = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getCountClientDemandeExportActive($client->getId());
        return $this->render('NzoTunisiefretBundle:Exportateur:ProfilClient.html.twig', array('client' => $client, 'demandes' => $demandes, 'active' => $active));
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ProfilPublicExportateurAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();           
        $postules = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE a.demande_accepter = 1 AND a.exportateur = ".$usr->getId()." ORDER BY d.date_tacking DESC");
        return $this->render('NzoTunisiefretBundle:Exportateur:ProfilExportateurPublic.html.twig', array('postules' => $postules->getResult() ));
    }
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function DetailPostuleActiveAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getExportateur() != $usr || $postule->getDemandeexport()->getTacking() || $postule->getDemandeexport()->getAnnulerDemande() || $postule->getAnnulerByExportateur() || $postule->getDemandeRefuser()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 

        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
   
        return $this->render('NzoTunisiefretBundle:Exportateur:DetailPostuleActive.html.twig', array('postule' => $postule, 'msgs' => $msgs));    
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ContratEnCoursDetailAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
       // security access 

        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
   
        return $this->render('NzoTunisiefretBundle:Exportateur:DetailPostuleEnCours.html.twig', array('postule' => $postule, 'msgs' => $msgs));    
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ContratTermineDetailAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getExportateur() != $usr || !$postule->getDemandeexport()->getTerminerDemande() || $postule->getDemandeexport()->getAnnulerDemande() || $postule->getAnnulerByExportateur() || $postule->getDemandeRefuser()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 

        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
   
        return $this->render('NzoTunisiefretBundle:Exportateur:DetailPostuleTermine.html.twig', array('postule' => $postule, 'msgs' => $msgs));    
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function DetailPostuleArchiveAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getExportateur() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access   
        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
   
        return $this->render('NzoTunisiefretBundle:Exportateur:DetailPostuleArchive.html.twig', array('postule' => $postule, 'msgs' => $msgs));    
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function ListeNotificationsAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifExportateur($usr);
            $paginator = $this->get('knp_paginator'); 
            $listenotifications = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 8);         
            return $this->render('NzoTunisiefretBundle:Exportateur:ListNotifications.html.twig', array('listenotifications' => $listenotifications));
    }

    /**
     * @Secure(roles="ROLE_EXPORTATEUR")
     */
    public function AjaxGetNotifAction(Request $request) 
    {
        if ($request->isXmlHttpRequest()) {  
            
            $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();           
                
            $notifstotal = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifAjaxExportateur($usr);    
            if($notifstotal != NULL){
                $i = 0;
                foreach ($notifstotal as $res) {
                    $notifdate = $res->getDate()->format('d/m/Y H:i');
                    $val[$i] = array('date' => $notifdate, 'notiftext' => $res->getText(), 'notifvu' => $res->getVu(), 'url' => $res->getUrl());
                    $i++;
                }
            }
            else
                $val = array('vide');
            
            // set Vu to True
            $notifs = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifAjaxExportateurNonVu($usr);
            foreach ($notifs as $res) {
                    $res->setVu(true);
                    $em->persist($res);           
                }
                $em->flush();
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
            $id = $request->request->get('idmsg');                    
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
            $notifmsg->setDemandeexportpostule($postule);
            $notifmsg->setLogoemetteur($usr->getLogoname());
            $notifmsg->setTitredemandeexport($postule->getDemandeexport()->getTitre());
            $notifmsg->setText($msg);   
            $url = $this->get('router')->generate('client_notifmsg_url_val', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())), true);             
            $notifmsg->setUrl($url);
            
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
    public function MessageUrlValeurAction(DemandeExportPostule $postule)
    {
        $em = $this->getDoctrine()->getManager();   
        $query = $em->getRepository('NzoTunisiefretBundle:NotifMsg')->findBy(array('exportateur' => $postule->getExportateur(), 'demandeexportpostule' => $postule->getId()));

            foreach($query as $res){
                if(!$res->getVumsg())
                    $res->setVumsg(true);
                    $em->persist($res);
            }
            $em->flush();
            if($postule->getDemandeexport()->getTerminerDemande())
            $url = $this->get('router')->generate('exp_contrat_termine_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt( $postule->getId())));    
            else if($postule->getDemandeexport()->getTacking())
            $url = $this->get('router')->generate('exp_contrat_encours_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt( $postule->getId())));
            else if($postule->getDemandeexport()->getAnnulerDemande())
            $url = $this->get('router')->generate('exp_detail_postule_archive', array('id' => $this->get('nzo_url_encryptor')->encrypt( $postule->getId())));
            else    
            $url = $this->get('router')->generate('exp_detail_postule_active', array('id' => $this->get('nzo_url_encryptor')->encrypt( $postule->getId())));
            
            return $this->redirect($url);
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
            $postule->getDemandeexport()->setNombredepostule($postule->getDemandeexport()->getNombredepostule()-1);
            $em->remove($postule);

            // notif Client
                $notif = new Notification();
                $notif->setClient($postule->getDemandeexport()->getClient());
                //==================================================================================================================== lien exportateur pour info demande export + classe css
                $text = $postule->getExportateur()->getNomentrop().' a supprimé son postule pour la Demande <span>'.$postule->getDemandeexport()->getTitre().'</span>';
                $notif->setText($text);                  
                $notif->setUrl('#');
                $em->persist($notif);
            
            $em->flush();
            
            //Email   
            $textmail = $postule->getExportateur()->getNomentrop().' a supprimé son postule pour la Demande <span>'.$postule->getDemandeexport()->getTitre().'</span>';
            $this->get('nzo.mailer')->NzoSendMail($postule->getDemandeexport()->getClient()->getEmail(), 10, $postule->getDemandeexport()->getClient()->getNomentrop(), $textmail );
                
            $this->get('session')->getFlashBag()->set('nzonotice', 'Postule Annulé avec succès');
            return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
  }
  
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function DonnerAvisDemandeExportAction(DemandeExportPostule $postule, Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getExportateur() != $usr || !$postule->getDemandeexport()->getTerminerDemande()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
            if( $postule->getDemandeexport()->getAvisExport()){
                if( $postule->getDemandeexport()->getAvisExport()->getNoteclient() != NULL)
                    return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
            }
       // security access 
        if($request->getMethod() === 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            
            if( $postule->getDemandeexport()->getAvisExport() ){
                $avis = $postule->getDemandeexport()->getAvisExport();
                $avis->setAvisclient($request->request->get('avisexportateur'));
                $avis->setNoteclient( round($request->request->get('noteexportateur'),2) );
                $em->persist($avis);               
            }
            else{
                $avis = new AvisExport();
                $avis->setAvisclient($request->request->get('avisexportateur'));
                $avis->setNoteclient( round($request->request->get('noteexportateur'),2) );
                $em->persist($avis);
                $postule->getDemandeexport()->setAvisExport($avis);
            }
            // update note Client
            if($postule->getDemandeexport()->getClient()->getNote() == -1){
                $postule->getDemandeexport()->getClient()->setNote( $avis->getNoteclient() );
            }
            else{
                $notefinal = ( $postule->getDemandeexport()->getClient()->getNote() + $avis->getNoteclient() )/2;
                $postule->getDemandeexport()->getClient()->setNote( round($notefinal,2) );
            }
            // notif Client
            $notif = new Notification();
            $notif->setClient($postule->getDemandeexport()->getClient());
            //==================================================================================================================== lien exportateur pour avis export + classe css
            $text = 'Vous avez reçu un Avis sur le Contrat <span>'.$postule->getDemandeexport()->getTitre().'</span>';
            $notif->setText($text);
            $url = $this->get('router')->generate('client_notif_url_val', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())), true);              
            $notif->setUrl($url);
            $em->persist($notif);
            
            $em->flush();
            
            //Email   
            $textmail = 'Vous avez reçu un Avis sur le Contrat <a href="'.$url.'"><span>'.$postule->getDemandeexport()->getTitre().'</span></a>';
            $this->get('nzo.mailer')->NzoSendMail($postule->getDemandeexport()->getClient()->getEmail(), 11, $postule->getDemandeexport()->getClient()->getNomentrop(), $textmail );
                
            $this->get('session')->getFlashBag()->set('nzonotice', 'Avis associé avec succès');
            return $this->redirect($this->generateUrl('exp_contrat_termine_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId()))));   
        }

        return $this->render('NzoTunisiefretBundle:Exportateur:DonnerAvisExport.html.twig', array('postule' => $postule));
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function RechercheAction($type)
    {
        $mot = $this->getRequest()->query->get('mot');
       if($type=='Active') {
           $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        
        $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE (d.description LIKE :mot OR d.titre LIKE :mot) AND d.tacking = 0 AND d.annuler_demande is NULL AND a.demande_refuser = 0 AND a.annuler_by_exportateur = 0 AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
        $query->setParameter('mot', "%$mot%");
        
        $paginator = $this->get('knp_paginator'); 
        $listepostules = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Exportateur:ResultatRecherche.html.twig', array('listepostules' => $listepostules));
       }
       else if($type=='Archive') {         
           $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();   
            
            $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE (d.description LIKE :mot OR d.titre LIKE :mot) AND (d.tacking = 0 OR d.annuler_demande is NOT NULL OR a.demande_refuser = 1 OR a.annuler_by_exportateur = 1) AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
            $query->setParameter('mot', "%$mot%");
            
            $paginator = $this->get('knp_paginator'); 
            $listepostules = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Exportateur:ResultatRecherche.html.twig', array('listepostules' => $listepostules));
       }
       else if($type=='EnCours') {
           $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager(); 
            
            $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE (d.description LIKE :mot OR d.titre LIKE :mot) AND d.tacking = 1 AND d.terminer_demande is NULL AND d.annuler_demande is NULL AND a.demande_refuser = 0 AND a.annuler_by_exportateur = 0 AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
            $query->setParameter('mot', "%$mot%");
            
            $paginator = $this->get('knp_paginator'); 
            $listepostules = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Exportateur:ResultatRechercheEnCours.html.twig', array('listepostules' => $listepostules));
        
       }
       else if($type=='Termine') {
            $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();   
            
            $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE (d.description LIKE :mot OR d.titre LIKE :mot) AND d.terminer_demande is NOT NULL AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
            $query->setParameter('mot', "%$mot%");
            
            $paginator = $this->get('knp_paginator'); 
            $listepostules = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Exportateur:ResultatRechercheEnCours.html.twig', array('listepostules' => $listepostules));
       }
       else       
         return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));  
    }
    
    /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function SignalerDemandeAction(DemandeExport $demande, Request $request)
    {   
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        $titre = $request->request->get('titre');
        $description = $request->request->get('description');  
        $signalisation = new \Nzo\TunisiefretBundle\Entity\Signalisation;
        $signalisation->setClient($demande->getClient());
        $signalisation->setDemandeexport($demande);
        $signalisation->setExportateur($usr);
        $signalisation->setTitre($titre);
        $signalisation->setDescription($description);
        $signalisation->setType('Signal Demande');        
        $em->persist($signalisation);
        
        //Notification Admin
        $notif = new Notification();
        $admin = $em->getRepository('NzoUserBundle:Admin')->find(10);
        $notif->setAdmin($admin);
        $notif->setText('Demande Signalé');
        $url = $this->get('router')->generate('admin_liste_demande_signiale');              
        $notif->setUrl($url);
        $em->persist($notif);
        
        $em->flush();
        //$this->container->get('nzo.mailer')->NzoSendMail($admin->getEmail(), 13);
        $this->get('session')->getFlashBag()->set('nzonotice', 'Votre signalisation est envoyer à l\'administrateur');
        return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
    }
} 
