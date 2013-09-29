<?php
namespace Nzo\TunisiefretBundle\Listener; 
use Symfony\Component\HttpKernel\HttpKernelInterface; 
use Symfony\Component\HttpKernel\Event\FilterControllerEvent; 

class ControllerListener 
{	 
    public function onCoreController(FilterControllerEvent $event) 	
    {
        // Récupération de l'event 	
        if(HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) 
        {
            // Récupération du controller    
            $_controller = $event->getController();
            if (isset($_controller[0])) 
            {
                // On vérifie que le controller implémente la méthode preExecute
                if(method_exists($_controller[0],'preExecute'))
                {
                    $_controller[0]->preExecute();
                }
            }
        }
 
    }
}