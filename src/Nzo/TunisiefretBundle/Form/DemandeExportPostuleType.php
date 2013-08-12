<?php

namespace Nzo\TunisiefretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DemandeExportPostuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)

    {
	    $builder
                ->add('duree')
                ->add('description') 
                ->add('prix')       
            ;            
           
    }

    public function getName()
    {
        return 'nzodemandeexportpostule';
    }
   
}