<?php

namespace Nzo\TunisiefretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MsgDemandeExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)

    {
	    $builder
                ->add('message')      
            ;            
           
    }

    public function getName()
    {
        return 'nzomsgdemandeexport';
    }
   
}