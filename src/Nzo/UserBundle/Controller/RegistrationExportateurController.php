<?php

namespace Nzo\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class RegistrationExportateurController extends BaseController
{
    public function registerAction()
    {
        return $this->container
                    ->get('pugx_multi_user.registration_manager')
                    ->register('Nzo\UserBundle\Entity\Exportateur');
    }
}