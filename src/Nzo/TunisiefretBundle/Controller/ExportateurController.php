<?php

namespace Nzo\TunisiefretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Nzo\TunisiefretBundle\Form\DemandeExportPostuleType;
use Nzo\TunisiefretBundle\Entity\DemandeExportPostule;
use Nzo\TunisiefretBundle\Entity\DemandeExport;
use Nzo\TunisiefretBundle\Entity\Notification;

use JMS\SecurityExtraBundle\Annotation\Secure;

class ExportateurController extends Controller {
    
   /**
    * @Secure(roles="ROLE_EXPORTATEUR")
    */
    public function PostuleDemandeExportAction(DemandeExport $DemandeExport, Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $PostuleExport = new DemandeExportPostule();
        $PostuleExport->setExportateur($usr);
        $PostuleExport->setDemandeexport($DemandeExport);
        $form = $this->createForm(new DemandeExportPostuleType(), $PostuleExport);
        if ($this->getRequest()->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                //augmente NB postule sur la Demande
                $DemandeExport->setNombredepostule($DemandeExport->getNombredepostule()+1);
                //Notification Client
                $Notification = new Notification();
                $Notification->setClient($DemandeExport->getClient());
                $Notification->setText('Nouveau Postule sur la Demande '.$DemandeExport->getReference());
                // end Notification
                //augmente NB postule de l'Exportateur
                $usr->setNbdemandeexportpostuler($usr->getNbdemandeexportpostuler()+1);   
                $em->persist($Notification);
                $em->persist($PostuleExport);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'c boooonn!');
                return $this->redirect($this->generateUrl('nzo_tunisiefret_homepage'));
            }
        }
        return $this->render('NzoTunisiefretBundle:Exportateur:PostuleDemandeExport.html.twig', array('form' => $form->createView()));
    }

}
