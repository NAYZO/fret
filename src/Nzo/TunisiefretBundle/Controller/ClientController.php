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
use Nzo\TunisiefretBundle\Entity\Notification;
use Nzo\TunisiefretBundle\Entity\AnnulerDemandeExport;
use Nzo\TunisiefretBundle\Entity\TerminerDemandeExport;
use Nzo\TunisiefretBundle\Entity\AvisExport;

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
        if ($request->getMethod() === 'POST') {
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
    public function TerminerDemandeExportAction(DemandeExport $mydemande, Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($mydemande->getClient() != $usr || !$mydemande->getTacking()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
            $em = $this->getDoctrine()->getManager();
            $terminer = new TerminerDemandeExport();
            $terminer->setResultat( $request->request->get('terminer_resultat') );
            $terminer->setDescription( $request->request->get('terminer_description') );
            $em->persist($terminer);
            // save Teminer object in DemandeExport
            $mydemande->setTerminerDemande($terminer);
            $em->persist($mydemande);
            
            // notif Exportateur
            $listepostules = $mydemande->getDemandeexportpostule();
            foreach($listepostules as $postule)
            {
                if($postule->getDemandeAccepter())
                {
                    $notifmsg = new Notification();
                    $notifmsg->setExportateur($postule->getExportateur());
                    //==================================================================================================================== lien exportateur pour info demande export + classe css
                    $text = 'Demande Export <a href=""> <span>'.$mydemande->getTitre().'</span> </a> est Terminé!';
                    $notifmsg->setText($text);
                    $em->persist($notifmsg);
                    $postuleexport = $postule;
                }
            }
            
            $em->flush();
        return $this->redirect($this->generateUrl('client_donner_avis_demande_export', array('id' => $postuleexport)));   
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
            
            // notif Exportateurs
            $listepostules = $mydemande->getDemandeexportpostule();
            foreach($listepostules as $postule)
            {
                $notifmsg = new Notification();
                $notifmsg->setExportateur($postule->getExportateur());
                //==================================================================================================================== lien exportateur pour info demande export + classe css
                $text = 'Demande Export <a href=""> <span>'.$mydemande->getTitre().'</span> </a> est annulé!';
                $notifmsg->setText($text);
                $em->persist($notifmsg);
            }
            
            $em->flush();
        return $this->redirect($this->generateUrl('nzo_voirlistedemande_export_active'));   
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
        if ($request->getMethod() === 'POST') {
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
    
    /**
    * @Secure(roles="ROLE_CLIENT")
    */
    public function DonnerAvisDemandeExportAction(DemandeExportPostule $postule, Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
       // security access     
            if($postule->getDemandeexport()->getClient() != $usr || !$postule->getDemandeexport()->getTerminerDemande()) return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
       // security access 
        if($request->getMethod() === 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            ( $postule->getDemandeexport()->getAvisExport() ) ? $avis = $postule->getDemandeexport()->getAvisExport() : $avis = new AvisExport();
            $avis->setAvisclient($request->request->get('avisclient'));
            $avis->setNoteclient($request->request->get('noteclient'));
            $em->persist($avis);
            
            // notif Exportateur
            $notifmsg = new Notification();
            $notifmsg->setExportateur($postule->getExportateur());
            //==================================================================================================================== lien exportateur pour avis export + classe css
            $text = 'Vous avez reçu un Avis sur le Contrat Export <a href=""> <span>'.$postule->getDemandeexport()->getTitre().'</span> </a>';
            $notifmsg->setText($text);
            $em->persist($notifmsg);
            
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'c boooonn!');
            return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
        }

        return $this->render('NzoTunisiefretBundle:Client:DonnerAvisExport.html.twig', array('demande' => $postule));
    }
}
