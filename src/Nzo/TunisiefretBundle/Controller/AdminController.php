<?php

namespace Nzo\TunisiefretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Httpfoundation\Response;

use Nzo\UserBundle\Entity\Client;
use Nzo\TunisiefretBundle\Entity\DemandeExport;
use Nzo\TunisiefretBundle\Entity\DemandeExportPostule;
use Nzo\TunisiefretBundle\Entity\Notification;

class AdminController extends Controller {
    
    /**
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_ADMIN")
     */
    public function AjaxGetNbNotifAction(Request $request) 
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $nbnotifs = $em->getRepository('NzoTunisiefretBundle:Notification')->getNbNotifAdmin($usr);
        if ($request->isXmlHttpRequest()) 
        return new Response($nbnotifs);
        return new Response($nbnotifs);
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function homeAction()
    {
        $nb_demande_import=0;
        $nb_demande_import_active=0;
        $nb_demande_import_archive=0;
        $nb_demande_import_encours=0;
        $nb_demande_import_termine=0;
        
        $nb_demande_export=0;
        $nb_demande_export_active=0;
        $nb_demande_export_archive=0;
        $nb_demande_export_encours=0;
        $nb_demande_export_termine=0;

        $em = $this->getDoctrine()->getManager();        
        $demandefrets = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->findAll(); 
        $clients = $em->getRepository('NzoUserBundle:Client')->findAll(); 
        $affreteurs = $em->getRepository('NzoUserBundle:Exportateur')->findAll();
        $demandefret_active = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getAllDemandeFretActive(); 
        $demandefret_encours = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getAllDemandeFretEnCours(); 
        $demandefret_termine = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getAllDemandeFretTermine(); 
        
        foreach($demandefrets as $val){
            if($val->getType()=='Import'){
                $nb_demande_import+=1;
                if(!$val->getTacking() && $val->getAnnulerDemande() == null)
                    $nb_demande_import_active+=1;
                else if(!$val->getTacking() && $val->getAnnulerDemande() != null)
                    $nb_demande_import_archive+=1;
                else if($val->getTacking() && $val->getTerminerDemande() == null)
                    $nb_demande_import_encours+=1;
                else if($val->getTerminerDemande() != null)
                    $nb_demande_import_termine+=1;
            }
            else{
                $nb_demande_export+=1;
                if(!$val->getTacking() && $val->getAnnulerDemande() == null)
                    $nb_demande_export_active+=1;
                else if(!$val->getTacking() && $val->getAnnulerDemande() != null)
                    $nb_demande_export_archive+=1;
                else if($val->getTacking() && $val->getTerminerDemande() == null)
                    $nb_demande_export_encours+=1;
                else if($val->getTerminerDemande() != null)
                    $nb_demande_export_termine+=1;                
            }
        }
        return $this->render('NzoTunisiefretBundle:Admin:index.html.twig', 
                array('nbclients' =>count($clients), 'nbaffreteurs' =>count($affreteurs), 'nb_demande_export' =>$nb_demande_export,
                    'nb_demande_import' => $nb_demande_import, 'nb_demande_import_active' =>$nb_demande_import_active,
                    'nb_demande_import_archive'=>$nb_demande_import_archive, 'nb_demande_import_encours'=>$nb_demande_import_encours,
                    'nb_demande_import_termine' =>$nb_demande_import_termine, 'nb_demande_export_active' =>$nb_demande_export_active,
                'nb_demande_export_archive' =>$nb_demande_export_archive, 'nb_demande_export_encours' =>$nb_demande_export_encours,
                    'nb_demande_export_termine' => $nb_demande_export_termine,
                    'demandefret_active' =>$demandefret_active, 'demandefret_encours' =>$demandefret_encours, 'demandefret_termine' =>$demandefret_termine));
    }
    
    
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
        $listeaffreteurs = $paginator->paginate($query,
        $this->get('request')->query->get('page', 1), 6);         
        return $this->render('NzoTunisiefretBundle:Admin:ListeAffreteurs.html.twig', array('listeaffreteurs' => $listeaffreteurs));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function ListeDemandeActiveAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getDemandeExportActiveAdmin();
            $paginator = $this->get('knp_paginator'); 
            $listedemandeexport = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Admin:ListeDemandeExportActive.html.twig', array('listedemandeexport' => $listedemandeexport));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function ActiveClientAction(Client $id)
    {
        $id->setEnabled(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($id);
        $em->flush();

        $this->get('nzo.mailer')->NzoSendMail($id->getEmail(), 3, $id->getNomentrop());

        $this->get('session')->getFlashBag()->set('nzonotice', 'Compte Activé!');
        $url = $this->get('request')->headers->get('referer');
                      if(empty($url)) {
                      $url = $this->generateUrl('admin_home');
                      }
        return $this->redirect( $url );
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DesactiveClientAction(Client $id)
    {
        $id->setEnabled(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($id);
        $em->flush();
        
        $this->get('nzo.mailer')->NzoSendMail($id->getEmail(), 4, $id->getNomentrop());

        $this->get('session')->getFlashBag()->set('nzonotice', 'Compte Désactivé!');
        $url = $this->get('request')->headers->get('referer');
                      if(empty($url)) {
                      $url = $this->generateUrl('admin_home');
                      }
        return $this->redirect( $url );
    }
    
   /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DetailClientAction(Client $id)
    {
        $em = $this->getDoctrine()->getManager();   
        $demandes = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->findBy(array('client' => $id->getId(), 'tacking' => 1), array('date_tacking' => 'DESC'));
        $active = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getCountClientDemandeExportActive($id->getId());
        return $this->render('NzoTunisiefretBundle:Admin:DetailClient.html.twig', array('client' => $id, 'demandes' => $demandes, 'active' => $active));
    }
  
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DemandeExportActiveAction(DemandeExport $mydemande)
    {  
        $em = $this->getDoctrine()->getManager();        
        $postules = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->getDemandeExportPostuleByDemande($mydemande);
        return $this->render('NzoTunisiefretBundle:Admin:DemandeExportActive.html.twig', array('mydemande' => $mydemande, 'postules' => $postules));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function ActiveAffreteurAction(\Nzo\UserBundle\Entity\Exportateur $id)
    {
        $id->setEnabled(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($id);
        $em->flush();

        $this->get('nzo.mailer')->NzoSendMail($id->getEmail(), 3, $id->getNomentrop());

        $this->get('session')->getFlashBag()->set('nzonotice', 'Compte Activé!');
        $url = $this->get('request')->headers->get('referer');
                      if(empty($url)) {
                      $url = $this->generateUrl('admin_home');
                      }
        return $this->redirect( $url );
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DesactiveAffreteurAction(\Nzo\UserBundle\Entity\Exportateur $id)
    {
        $id->setEnabled(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($id);
        $em->flush();
        
        $this->get('nzo.mailer')->NzoSendMail($id->getEmail(), 4, $id->getNomentrop());

        $this->get('session')->getFlashBag()->set('nzonotice', 'Compte Désactivé!');
        $url = $this->get('request')->headers->get('referer');
                      if(empty($url)) {
                      $url = $this->generateUrl('admin_home');
                      }
        return $this->redirect( $url );
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DetailAffreteurAction(\Nzo\UserBundle\Entity\Exportateur $id)
    {
        $em = $this->getDoctrine()->getManager();           
        $postules = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE a.demande_accepter = 1 AND a.exportateur = ".$id->getId()." ORDER BY d.date_tacking DESC");
        return $this->render('NzoTunisiefretBundle:Admin:DetailAffreteur.html.twig', array('affreteur' => $id, 'postules' => $postules->getResult() ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function AjaxGetNotifAction(Request $request) 
    {
        if ($request->isXmlHttpRequest()) {  
            
            $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();           
                
            $notifstotal = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifAjaxAdmin($usr);    
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
            $notifs = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifAjaxAdminNonVu($usr);
            foreach ($notifs as $res) {
                    $res->setVu(true);
                    $em->persist($res);           
                }
                $em->flush();
        return new Response(json_encode($val));
        }
    }  
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function ListeNotificationsAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifAdmin($usr);
            $paginator = $this->get('knp_paginator'); 
            $listenotifications = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 8);         
            return $this->render('NzoTunisiefretBundle:Admin:ListNotifications.html.twig', array('listenotifications' => $listenotifications));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DetailPostuleActiveAction(DemandeExportPostule $postule)
    { 
        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
        return $this->render('NzoTunisiefretBundle:Admin:DetailPostuleActive.html.twig', array('postule' => $postule, 'msgs' => $msgs));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function ListeContratEncoursAction()
    {
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getContratEncoursAdmin();
            $paginator = $this->get('knp_paginator'); 
            $listecontratencours = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Admin:ListeContratEncours.html.twig', array('listecontratencours' => $listecontratencours));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DemandeExportEnCoursDetailAction(DemandeExport $mydemande)
    {
        $tt = $mydemande->getDemandeexportpostule();
            foreach($tt as $res){
               if($res->getDemandeAccepter())
                  $em = $this->getDoctrine()->getManager();        
                  $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $res)); 
                return $this->render('NzoTunisiefretBundle:Admin:DetailPostuleEnCours.html.twig', array('postule' => $res, 'msgs' => $msgs));
            }          
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function ListeDemandeArchiveAction()
    {
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getDemandeExportArchiveAdmin();
            $paginator = $this->get('knp_paginator'); 
            $listedemandeexport = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Admin:ListeDemandeExportArchive.html.twig', array('listedemandeexport' => $listedemandeexport));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DemandeExportArchiveAction(DemandeExport $mydemande)
    { 
        $em = $this->getDoctrine()->getManager();
        $postules = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->getDemandeExportPostuleByDemande($mydemande);
        return $this->render('NzoTunisiefretBundle:Admin:DemandeExportArchive.html.twig', array('mydemande' => $mydemande, 'postules' => $postules));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DetailPostuleArchiveAction(DemandeExportPostule $postule)
    {
        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
        return $this->render('NzoTunisiefretBundle:Admin:DetailPostuleArchive.html.twig', array('postule' => $postule, 'msgs' => $msgs));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function ListeContratTerminerAction()
    {
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getContratTerminerAdmin();
            $paginator = $this->get('knp_paginator'); 
            $listecontratterminer = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Admin:ListeContratTerminer.html.twig', array('listecontratterminer' => $listecontratterminer));
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function DemandeExportTermineDetailAction(DemandeExport $mydemande)
    { 
        $tt = $mydemande->getDemandeexportpostule();
            foreach($tt as $res){
               if($res->getDemandeAccepter())
                  $em = $this->getDoctrine()->getManager();        
                  $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $res)); 
                return $this->render('NzoTunisiefretBundle:Admin:DetailPostuleTermine.html.twig', array('postule' => $res, 'msgs' => $msgs));
            }          
    }
    
    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function SupprimerDemandeAction(DemandeExport $mydemande)
    { 
            $em = $this->getDoctrine()->getManager();            
            
            // notif Affréteurs
            $listepostules = $mydemande->getDemandeexportpostule();
            foreach($listepostules as $postule)
            {
                $notif = new Notification();
                $notif->setExportateur($postule->getExportateur());                
                $text = 'Demande de Fret <span>'.$mydemande->getTitre().'</span> est supprimé par l\'Administrateur!';
                $notif->setText($text);
                $notif->setUrl('#');
                $em->persist($notif);
                
                //Email 
                $textmail = 'Demande de Fret <span>'.$mydemande->getTitre().'</span> est supprimé par l\'Administrateur!';
                $this->get('nzo.mailer')->NzoSendMail($postule->getExportateur()->getEmail(), 12, $postule->getExportateur()->getNomentrop(), $textmail );
            }
            // notif Client
                $notif = new Notification();
                $notif->setClient($mydemande->getClient());                
                $text = 'Demande de Fret <span>'.$mydemande->getTitre().'</span> est supprimé par l\'Administrateur!';
                $notif->setText($text);
                $notif->setUrl('#');
                $em->persist($notif);
                
                //Email 
                $textmail = 'Demande de Fret <span>'.$mydemande->getTitre().'</span> est supprimé par l\'Administrateur!';
                $this->get('nzo.mailer')->NzoSendMail($mydemande->getClient()->getEmail(), 12, $mydemande->getClient()->getNomentrop(), $textmail );
                
            $em->remove($mydemande);
            $em->flush();
            
            $this->get('session')->getFlashBag()->set('nzonotice', 'Demande de Fret Supprimé!');
            return $this->redirect( $this->generateUrl('admin_home') );
    }
}
