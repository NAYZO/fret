<?php

namespace Nzo\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationExportateurFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
            $builder
                ->add('nomentrop')            
                ->add('ville')   
                ->add('adresse', 'textarea')  
                ->add('tel')    
                ->add('fax')  
                ->add('siteweb')  
                ->add('description')   
                ->add('uploadlogo', 'file',  array('required' => false))
        ;
    }

    public function getName()
    {
        return 'nzo_exportateur_registration';
    }
}