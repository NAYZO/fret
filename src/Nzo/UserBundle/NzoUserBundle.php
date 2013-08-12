<?php

namespace Nzo\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NzoUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
