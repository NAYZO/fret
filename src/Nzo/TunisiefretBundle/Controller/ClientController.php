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

class ClientController extends Controller {
    
    /**
     * @Secure(roles="ROLE_CLIENT")
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
     * @Secure(roles="ROLE_CLIENT")
     */
    public function GetPostuleEncoursByDemandeAction($id) 
    {
        $id = $this->get('nzo_url_encryptor')->decrypt($id);
            $em = $this->getDoctrine()->getManager();
            $postule = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->GetPostuleEncoursByDemande($id);
        return new Response($postule);
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function NotificationUrlValeurAction(DemandeExportPostule $postule)
    {

            if($postule->getDemandeexport()->getTerminerDemande())
            $url = $this->get('router')->generate('client_demande_export_termine_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getDemandeexport()->getId())));    
            else if ($postule->getDemandeexport()->getTacking())    
            $url = $this->get('router')->generate('client_demande_export_encours_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getDemandeexport()->getId())));
            else if($postule->getDemandeexport()->getAnnulerDemande())
            $url = $this->get('router')->generate('client_postule_archive_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));                      
            else
            $url = $this->get('router')->generate('client_postule_active_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));                  
            
            return $this->redirect($url);
     }

   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function PoserDemandeExportAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $demande = new DemandeExport();
        $demande->setClient($usr);
        $form = $this->createForm(new DemandeExportType(), $demande);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $usr->setNbdemandeexportdepose($usr->getNbdemandeexportdepose()+1);
                $em->persist($demande);
                $em->flush();

                $this->get('session')->getFlashBag()->set('nzonotice', 'Demande de Fret enregistré avec succès');
                return $this->redirect($this->generateUrl('nzo_voirlistedemande_export_active'));
            }
        }
        return $this->render('NzoTunisiefretBundle:Client:PoserDemandeExport.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     */
    public function AjaxGetNbNotifAction(Request $request) 
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $nbnotifs = $em->getRepository('NzoTunisiefretBundle:Notification')->getNbNotifClient($usr);
        if ($request->isXmlHttpRequest()) 
        return new Response($nbnotifs);
        return new Response($nbnotifs);
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     */
    public function AjaxGetNbMsgAction(Request $request) 
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $nbmsgs = $em->getRepository('NzoTunisiefretBundle:NotifMsg')->getNbMsgClient($usr);
        if ($request->isXmlHttpRequest()) 
        return new Response($nbmsgs);
        return new Response($nbmsgs);
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ListeNotificationsAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifClient($usr);
            $paginator = $this->get('knp_paginator'); 
            $listenotifications = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 8);         
            return $this->render('NzoTunisiefretBundle:Client:ListNotifications.html.twig', array('listenotifications' => $listenotifications));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ListeMessagesAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:NotifMsg')->getListMessagesClient($usr);
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
            return $this->render('NzoTunisiefretBundle:Client:ListNotifMessages.html.twig', array('listemessages' => $listemessages));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ProfilExportateurAction($id)
    {
        $em = $this->getDoctrine()->getManager();   
        $usr = $em->getRepository('NzoUserBundle:Exportateur')->find($id);        
        // liste contrat en cours 
        $contratsCours = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d WHERE d.tacking = 1 AND d.terminer_demande is NULL AND d.annuler_demande is NULL AND a.demande_refuser = 0 AND a.annuler_by_exportateur = 0 AND a.exportateur = ".$usr->getId()." ORDER BY a.datepostule DESC ");
        
        $contratsTermine = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExportPostule a JOIN a.demandeexport d JOIN d.terminer_demande t WHERE d.terminer_demande is NOT NULL AND a.exportateur = ".$usr->getId()." ORDER BY t.date_jobend DESC ");            
   
        return $this->render('NzoTunisiefretBundle:Client:ProfilExportateur.html.twig', array('exportateur' => $usr, 'contratstermine' => $contratsTermine->getResult(), 'contratscours' => $contratsCours->getResult()));    
    }

    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ProfilPublicClientAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        // liste contat en cours 
        $contratsCours = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientContratEncours($usr->getId());
        //$demandesTermine = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->findBy(array('client' => $usr->getId(), 'tacking' => 1), array('date_tacking' => 'DESC'));
        $contratsTermine = $em->createQuery("SELECT a,d FROM NzoTunisiefretBundle:DemandeExport a JOIN a.terminer_demande d WHERE a.terminer_demande is NOT NULL AND a.client = ".$usr->getId()." ORDER BY d.date_jobend DESC ");            
        $active = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getCountClientDemandeExportActive($usr->getId());
        return $this->render('NzoTunisiefretBundle:Client:ProfilClientPublic.html.twig', array('contratstermine' => $contratsTermine->getResult(), 'contratscours' => $contratsCours->getResult(), 'active' => $active));
    }

    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function TerminerDemandeExportAction(DemandeExportPostule $postule, Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getDemandeExport()->getClient() != $usr || !$postule->getDemandeExport()->getTacking()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
            $em = $this->getDoctrine()->getManager();
            $terminer = new TerminerDemandeExport();
            $terminer->setResultat( $request->request->get('terminer_resultat') );
            $terminer->setDescription( $request->request->get('terminer_description') );
            $em->persist($terminer);
            // save Teminer object in DemandeExport
            $postule->getDemandeExport()->setTerminerDemande($terminer);
            // nbcontratterminer ++ exportateur
            $postule->getExportateur()->setNbcontrattermine($postule->getExportateur()->getNbcontrattermine()+1);
            // nbcontratencours -- exportateur
            $postule->getExportateur()->setNbcontratencours($postule->getExportateur()->getNbcontratencours()-1);
            // nbcontratterminer ++ Client
            $postule->getDemandeExport()->getClient()->setNbcontrattermine($usr->getNbcontrattermine()+1);
            // nbcontratencours -- Client
            $postule->getDemandeExport()->getClient()->setNbcontratencours($usr->getNbcontratencours()-1);
            $em->persist($postule);
            
            // notif Exportateur                   
                    $notif = new Notification();
                    $notif->setExportateur($postule->getExportateur());
                    //==================================================================================================================== lien exportateur pour info demande export + classe css
                    $text = 'Contrat de Fret <span>'.$postule->getDemandeExport()->getTitre().'</span> est Terminé!';
                    $notif->setText($text);
                    $url = $this->get('router')->generate('exp_contrat_termine_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));                  
                    $notif->setUrl($url);
                    $em->persist($notif);
            
            $em->flush();
            
            //Email
            $textmail = 'Contrat de Fret <a href="'.$url.'"><span>'.$postule->getDemandeExport()->getTitre().'</span></a> est Terminé!';
            $this->get('nzo.mailer')->NzoSendMail($postule->getExportateur()->getEmail(), 5, $postule->getExportateur()->getNomentrop(), $textmail );
            
            $this->get('session')->getFlashBag()->set('nzonotice', 'Demande de Fret terminé avec succès');
          
        return $this->redirect($this->generateUrl('client_donner_avis_demande_export', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId()))));   
  }
  
  /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ConfirmerContratExportAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getDemandeexport()->getClient() != $usr || $postule->getDemandeexport()->getTacking() || $postule->getDemandeexport()->getAnnulerDemande() || $postule->getDemandeRefuser() || $postule->getAnnulerByExportateur()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
            $em = $this->getDoctrine()->getManager();
            $postule->setDemandeAccepter(true);
            $postule->getDemandeexport()->setDateTacking(new \DateTime('now'));
            $postule->getDemandeexport()->setTacking(true);
            // nbcontratencours ++ exportateur
            $postule->getExportateur()->setNbcontratencours($postule->getExportateur()->getNbcontratencours()+1);
            // nbcontratencours ++ Client
            $postule->getDemandeExport()->getClient()->setNbcontratencours($usr->getNbcontratencours()+1);
            $em->persist($postule);
            // notif Exportateur
       
                    $notif = new Notification();
                    $notif->setExportateur($postule->getExportateur());
                    //==================================================================================================================== lien exportateur pour info demande export + classe css
                    $text = 'Votre contrat: <span>'.$postule->getDemandeexport()->getTitre()."</span> est commencé le ".$postule->getDemandeexport()->getDateTacking()->format('d/m/Y');
                    $notif->setText($text);
                    $url = $this->get('router')->generate('exp_notif_url_val', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())), true);   
                    $notif->setUrl($url);
                    $em->persist($notif);
            
            $em->flush();
            
            //Email
            $textmail = 'Votre contrat de fret: <a href="'.$url.'"><span>'.$postule->getDemandeexport()->getTitre()."</span></a> est commencé le ".$postule->getDemandeexport()->getDateTacking()->format('d/m/Y');
            $this->get('nzo.mailer')->NzoSendMail($postule->getExportateur()->getEmail(), 6, $postule->getExportateur()->getNomentrop(), $textmail );
            
        $this->get('session')->getFlashBag()->set('nzonotice', 'Votre Contrat de Fret est commencé');
          
        return $this->redirect($this->generateUrl('client_demande_export_encours_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getDemandeexport()->getId()))));   
  }
  
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function AnnulerDemandeExportAction(DemandeExport $mydemande, Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access
            $em = $this->getDoctrine()->getManager();
            $annuler = new AnnulerDemandeExport();
            $annuler->setRaison( $request->request->get('annuler_raison') );
            $annuler->setDescription( $request->request->get('annuler_description') );
            $em->persist($annuler);
            // save Annuler object in DemandeExport
            $mydemande->setAnnulerDemande($annuler);
            $em->persist($mydemande);
            
            // notif Affréteurs
            $listepostules = $mydemande->getDemandeexportpostule();
            foreach($listepostules as $postule)
            {
                $notif = new Notification();
                $notif->setExportateur($postule->getExportateur());
                //==================================================================================================================== lien exportateur pour info demande export + classe css
                $text = 'Demande de Fret <span>'.$mydemande->getTitre().'</span> est annulé!';
                $notif->setText($text);
                $url = $this->get('router')->generate('exp_notif_url_val', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())), true);    
                $notif->setUrl($url);
                $em->persist($notif);
                
                //Email 
                $textmail = 'Demande de Fret <a href="'.$url.'"><span>'.$mydemande->getTitre().'</span></a> est annulé!';
                $this->get('nzo.mailer')->NzoSendMail($postule->getExportateur()->getEmail(), 7, $postule->getExportateur()->getNomentrop(), $textmail );
            }
            
            $em->flush();   
            
            $this->get('session')->getFlashBag()->set('nzonotice', 'Votre demande de Fret est archivé');
        return $this->redirect($this->generateUrl('nzo_voirlistedemande_export_archive'));   
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
            $this->get('session')->getFlashBag()->set('nzonotice', 'Demande type supprimé!');
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
            
            $mydemande->setDemandeexporttype(false);
            
        $form = $this->createForm(new DemandeExportType(), $mydemande);
        if ($request->getMethod() === 'POST') {
            
            $mydemande->setDemandeexporttype(true);
            $demande = new DemandeExport();
            $form = $this->createForm(new DemandeExportType(), $demande);
            
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();               
                
                    $demande->setTacking(false);
                    $demande->setNombredepostule(0);
                    $demande->setAdresse( $form['adresse']->getData() );
                    $demande->setClient( $usr );
                    $demande->setCodepostal( $form['codepostal']->getData() );
                    $demande->setDatemax( $form['datemax']->getData() );            
                    $demande->setDescription( $form['description']->getData() ) ;
                    $demande->setPays( $form['pays']->getData() );
                    $demande->setPrix( $form['prix']->getData() );
                    $demande->setTitre( $form['titre']->getData() );
                    $demande->setVille( $form['ville']->getData() );
                
                $usr->setNbdemandeexportdepose($usr->getNbdemandeexportdepose()+1);    
                $em->persist($demande);
                $em->flush();

                $this->get('session')->getFlashBag()->set('nzonotice', 'Demande de Fret enregistré avec succès');
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
    public function ContratEncoursAction(DemandeExportPostule $postule)
    {
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->getDemandeExportPostuleByDemande($postule->getDemandeexport());
            $paginator = $this->get('knp_paginator'); 
            $listepostules = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:DemandeExportEnCours.html.twig', array('listepostules' => $listepostules, 'mydemande' => $postule->getDemandeexport()));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function ListeContratTerminerAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
           // $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientContratTerminer($usr->getId());            
            $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExport a JOIN a.terminer_demande d WHERE a.terminer_demande is NOT NULL AND a.client = ".$usr->getId()." ORDER BY d.date_jobend DESC ");            
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
            //$query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->getClientDemandeExportArchive($usr->getId());
            $query = $em->createQuery("SELECT a FROM NzoTunisiefretBundle:DemandeExport a JOIN a.annuler_demande d WHERE a.annuler_demande is NOT NULL AND a.tacking = 0 AND a.client = ".$usr->getId()." ORDER BY d.date_annuler DESC ");            
            $paginator = $this->get('knp_paginator'); 
            $listedemandeexport = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:ListeDemandeExportArchive.html.twig', array('listedemandeexport' => $listedemandeexport));
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DemandeExportActiveAction(DemandeExport $mydemande)
    { 
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $em = $this->getDoctrine()->getManager();        
        $postules = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->getDemandeExportPostuleByDemande($mydemande);
        return $this->render('NzoTunisiefretBundle:Client:DemandeExportActive.html.twig', array('mydemande' => $mydemande, 'postules' => $postules));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DetailPostuleActiveAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($postule->getDemandeexport()->getClient() != $usr || $postule->getDemandeexport()->getTacking() || $postule->getDemandeexport()->getAnnulerDemande() ) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
        return $this->render('NzoTunisiefretBundle:Client:DetailPostuleActive.html.twig', array('postule' => $postule, 'msgs' => $msgs));
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DemandeExportArchiveAction(DemandeExport $mydemande)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $em = $this->getDoctrine()->getManager();
        $postules = $em->getRepository('NzoTunisiefretBundle:DemandeExportPostule')->getDemandeExportPostuleByDemande($mydemande);
        return $this->render('NzoTunisiefretBundle:Client:DemandeExportArchive.html.twig', array('mydemande' => $mydemande, 'postules' => $postules));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DetailPostuleArchiveAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($postule->getDemandeexport()->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
        return $this->render('NzoTunisiefretBundle:Client:DetailPostuleArchive.html.twig', array('postule' => $postule, 'msgs' => $msgs));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DemandeExportEnCoursDetailAction(DemandeExport $mydemande)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($mydemande->getClient() != $usr || !$mydemande->getTacking() || $mydemande->getAnnulerDemande() || $mydemande->getTerminerDemande()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));          
       // security access 
        $tt = $mydemande->getDemandeexportpostule();
            foreach($tt as $res){
               if($res->getDemandeAccepter())
                  $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $res)); 
                  return $this->render('NzoTunisiefretBundle:Client:DetailPostuleEnCours.html.twig', array('postule' => $res, 'msgs' => $msgs));
            }          
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DemandeExportTermineDetailAction(DemandeExport $mydemande)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($mydemande->getClient() != $usr || !$mydemande->getTacking() || $mydemande->getAnnulerDemande() || !$mydemande->getTerminerDemande()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));          
       // security access 
        $tt = $mydemande->getDemandeexportpostule();
            foreach($tt as $res){
               if($res->getDemandeAccepter())
                  $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $res)); 
                  return $this->render('NzoTunisiefretBundle:Client:DetailPostuleTermine.html.twig', array('postule' => $res, 'msgs' => $msgs));
            }          
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DetailPostuleEnCoursAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($postule->getDemandeexport()->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
        return $this->render('NzoTunisiefretBundle:Client:DetailPostuleEnCours.html.twig', array('postule' => $postule, 'msgs' => $msgs));
    }
    
     /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DetailPostuleEnCoursAfterDoneAction(DemandeExportPostule $postule)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($postule->getDemandeexport()->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $em = $this->getDoctrine()->getManager();        
        $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));
        return $this->render('NzoTunisiefretBundle:Client:DetailPostuleEnCoursAfterDone.html.twig', array('postule' => $postule, 'msgs' => $msgs));
    }
    
   /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DetailDemandeExportTypeAction(DemandeExport $mydemande)
    {
        $usr = $this->get('security.context')->getToken()->getUser();       
       // security access     
            if($mydemande->getClient() != $usr) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access   
            
        return $this->render('NzoTunisiefretBundle:Client:DetailDemandeExportType.html.twig', array('mydemande' => $mydemande));
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     */
    public function MessageClientSendAction(Request $request) 
    {
        if ($request->isXmlHttpRequest()) {
            // save msg
            $msg = $request->request->get('msg');
            $id = $request->request->get('idmsg');                    
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

            $notifmsg = $em->getRepository('NzoTunisiefretBundle:NotifMsg')->findBy(array('exportateur' => $exportateur, 'demandeexportpostule' => $postule->getId()));
            if($notifmsg)
            {
                foreach($notifmsg as $res){
                    $res->setNbmsgnonvu( $res->getNbmsgnonvu()+1 );
                    $res->setText($msg); 
                    $res->setDate(new \DateTime('now')); 
                    $res->setVu(false);
                    $res->setVumsg(false);
                    $em->persist($res);                    
                }   
                $em->flush();
            }
            else
            {
                $notifmsg = new NotifMsg();
                $notifmsg->setNbmsgnonvu(1);
                $notifmsg->setExportateur($exportateur);
                $notifmsg->setDemandeexportpostule($postule);
                $notifmsg->setEmetteur($usr->getNomentrop());
                $notifmsg->setLogoemetteur($usr->getLogoname());
                $notifmsg->setTitredemandeexport($postule->getDemandeexport()->getTitre());
                $notifmsg->setText($msg); 
                $url = $this->get('router')->generate('exp_notifmsg_url_val', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));    
                $notifmsg->setUrl($url);
                $em->persist($notifmsg);
                $em->flush();
            }
            

           //  recuperation liste msg
           $msgs = $em->getRepository('NzoTunisiefretBundle:MsgDemandeExport')->findBy( array('demandeexportpostule' => $postule));

           $i = 0;
            foreach ($msgs as $res) {
                $msgdate = $res->getDate()->format('d/m/Y H:i');
                if($usr==$res->getClient() ) $msguser='Moi'; else $msguser=$postule->getExportateur()->getNomentrop();
                $val[$i] = array('date' =>$msgdate, 'msg' => $res->getMessage(), 'user' => $msguser);
                $i++;
            }
        return new Response(json_encode($val));
        }
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function MessageUrlValeurAction(DemandeExportPostule $postule)
    {
            $em = $this->getDoctrine()->getManager();   
            $query = $em->getRepository('NzoTunisiefretBundle:NotifMsg')->findBy(array('client' => $postule->getDemandeexport()->getClient(), 'demandeexportpostule' => $postule->getId()));

            foreach($query as $res){
                if(!$res->getVumsg()){
                    $res->setNbmsgnonvu(0);
                    $res->setVumsg(true);                    
                }
                    $em->persist($res);
            }
            $em->flush();
            if($postule->getDemandeexport()->getTerminerDemande())
            $url = $this->get('router')->generate('client_demande_export_termine_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getDemandeexport()->getId())));    
            else if ($postule->getDemandeexport()->getTacking())    
            $url = $this->get('router')->generate('client_demande_export_encours_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getDemandeexport()->getId())));
            else if($postule->getDemandeexport()->getAnnulerDemande())
            $url = $this->get('router')->generate('client_postule_archive_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));                      
            else
            $url = $this->get('router')->generate('client_postule_active_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())));                  
            
            return $this->redirect($url);
     }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DonnerAvisDemandeExportAction(DemandeExportPostule $postule, Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getDemandeexport()->getClient() != $usr || !$postule->getDemandeexport()->getTerminerDemande()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
            if( $postule->getDemandeexport()->getAvisExport()){
                if( $postule->getDemandeexport()->getAvisExport()->getNoteexportateur() != NULL)
                    return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
            }
       // security access 
        if($request->getMethod() === 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            
            if( $postule->getDemandeexport()->getAvisExport() ){
                $avis = $postule->getDemandeexport()->getAvisExport();
                $avis->setAvisexportateur($request->request->get('avisclient'));
                $avis->setNoteexportateur( round($request->request->get('noteclient'),2) );
                $em->persist($avis);               
            }
            else{
                $avis = new AvisExport();
                $avis->setAvisexportateur($request->request->get('avisclient'));
                $avis->setNoteexportateur( round($request->request->get('noteclient'),2) );
                $em->persist($avis);
                $postule->getDemandeexport()->setAvisExport($avis);
            }
            // update note Exportateur
            if($postule->getExportateur()->getNote() == -1){
                $postule->getExportateur()->setNote( $avis->getNoteexportateur() );
            }
            else{
                $notefinal = ( $postule->getExportateur()->getNote() + $avis->getNoteexportateur() )/2;
                $postule->getExportateur()->setNote( round($notefinal,2) );
            }
            // notif Exportateur
            $notif = new Notification();
            $notif->setExportateur($postule->getExportateur());
            //==================================================================================================================== lien exportateur pour avis export + classe css
            $text = 'Vous avez reçu un Avis sur le Contrat <span>'.$postule->getDemandeexport()->getTitre().'</span>';
            $notif->setText($text);
            $url = $this->get('router')->generate('exp_notif_url_val', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getId())), true);   
            $notif->setUrl($url);
            $em->persist($notif);
            
            $em->flush();
            
            //Email 
            $textmail = 'Vous avez reçu un Avis sur le Contrat <a href="'.$url.'"><span>'.$postule->getDemandeexport()->getTitre().'</span></a>';
            $this->get('nzo.mailer')->NzoSendMail($postule->getExportateur()->getEmail(), 8, $postule->getExportateur()->getNomentrop(), $textmail );
            
            $this->get('session')->getFlashBag()->set('nzonotice', 'Avis associé avec succès');
            return $this->redirect($this->generateUrl('client_demande_export_termine_detail', array('id' => $this->get('nzo_url_encryptor')->encrypt($postule->getDemandeexport()->getId()))));   
        }

        return $this->render('NzoTunisiefretBundle:Client:DonnerAvisExport.html.twig', array('postule' => $postule));
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     */
    public function AjaxGetNotifAction(Request $request) 
    {
        if ($request->isXmlHttpRequest()) {  
            
            $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();           
                
            $notifstotal = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifAjaxClient($usr);    
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
            $notifs = $em->getRepository('NzoTunisiefretBundle:Notification')->getListNotifAjaxClientNonVu($usr);
            foreach ($notifs as $res) {
                    $res->setVu(true);
                    $em->persist($res);           
                }
                $em->flush();
        return new Response(json_encode($val));
        }
    }  
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function RechercheAction($type)
    {
        $mot = $this->getRequest()->query->get('mot');
        //$mot = str_replace('’', '\'', $str);
       if($type=='Active') {
           $usr = $this->get('security.context')->getToken()->getUser();
           $em = $this->getDoctrine()->getManager();   
           $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->RechercheDemandeActive($usr->getId(), $mot);
                $paginator = $this->get('knp_paginator'); 
                $listedemandeexport = $paginator->paginate($query,
                $this->get('request')->query->get('page', 1), 6);         
           return $this->render('NzoTunisiefretBundle:Client:ResultatRecherche.html.twig', array('listedemandeexport' => $listedemandeexport));
       }
       else if($type=='Archive') {         
           $usr = $this->get('security.context')->getToken()->getUser();
           $em = $this->getDoctrine()->getManager();   
           $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->RechercheDemandeArchive($usr->getId(), $mot);
                $paginator = $this->get('knp_paginator'); 
                $listedemandeexport = $paginator->paginate($query,
                $this->get('request')->query->get('page', 1), 6);         
           return $this->render('NzoTunisiefretBundle:Client:ResultatRecherche.html.twig', array('listedemandeexport' => $listedemandeexport));
       }
       else if($type=='EnCours') {
           $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->RechercheDemandeEnCours($usr->getId(), $mot);
            $paginator = $this->get('knp_paginator'); 
            $listecontratencours = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:ResultatRechercheEnCours.html.twig', array('listecontratencours' => $listecontratencours));
        
       }
       else if($type=='Termine') {
            $usr = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();        
            $query = $em->getRepository('NzoTunisiefretBundle:DemandeExport')->RechercheDemandeTerminer($usr->getId(), $mot);
            $paginator = $this->get('knp_paginator'); 
            $listecontratencours = $paginator->paginate($query,
            $this->get('request')->query->get('page', 1), 6);         
            return $this->render('NzoTunisiefretBundle:Client:ResultatRechercheEnCours.html.twig', array('listecontratencours' => $listecontratencours));
       }
       else       
         return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));  
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function SignalerPostuleAction(DemandeExportPostule $postule, Request $request)
    {   
        $usr = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();   
        $titre = $request->request->get('titre');
        $description = $request->request->get('description');  
        $signalisation = new \Nzo\TunisiefretBundle\Entity\Signalisation;
        $signalisation->setClient($usr);
        $signalisation->setDemandeexportpostule($postule);
        $signalisation->setExportateur($postule->getExportateur());
        $signalisation->setTitre($titre);
        $signalisation->setDescription($description);
        $signalisation->setType('Signal Postule');        
        $em->persist($signalisation);
        //Notification Admin
        $notif = new Notification();
        $admin = $em->getRepository('NzoUserBundle:Admin')->find(10);
        $notif->setAdmin($admin);
        $notif->setText('Postule Signalé');
        $url = $this->get('router')->generate('admin_liste_demande_signiale');              
        $notif->setUrl($url);
        $em->persist($notif);
        
        $em->flush();
        $this->container->get('nzo.mailer')->NzoSendMail($admin->getEmail(), 14);
        $em->flush();
        $this->get('session')->getFlashBag()->set('nzonotice', 'Votre signalisation est envoyer à l\'administrateur');
        return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function SupprimerNotificationAction(Notification $notif)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($notif->getClient() != $usr ) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $em = $this->getDoctrine()->getManager();        
        $em->remove($notif);
        $em->flush();
        $this->get('session')->getFlashBag()->set('nzonotice', 'Notification Supprimé avec succès');
        $url = $this->get('request')->headers->get('referer');
                      if(empty($url)) {
                      $url = $this->generateUrl('client_list_notifications');
                      }
                      return $this->redirect( $url );        
    }
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function SupprimerMsgAction(NotifMsg $notif)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
           if($notif->getClient() != $usr ) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        $em = $this->getDoctrine()->getManager();        
        $em->remove($notif);
        $em->flush();
        $this->get('session')->getFlashBag()->set('nzonotice', 'Notification Supprimé avec succès');
        $url = $this->get('request')->headers->get('referer');
                      if(empty($url)) {
                      $url = $this->generateUrl('client_list_messages');
                      }
                      return $this->redirect( $url );        
    }
}
