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
                ->add('adresse', 'textarea')  
                ->add('tel')    
                ->add('fax')  
                ->add('siteweb')  
                //->add('licence', 'checkbox')
                ->add('description')   
                ->add('uploadlogo', 'file',  array('required' => false))
                ->add('ville', 'choice', array(                   
                    'choices' => array(
                        'Bizerte'=>'Bizerte',
                        'Djerba'=>'Djerba',
                        'Douz'=>'Douz',
                        'El kef'=>'El kef',
                        'Gabes'=>'Gabes',
                        'Gafsa'=>'Gafsa',
                        'Gasrine'=>'Gasrine',
                        'Hammamet'=>'Hammamet',
                        'Jandouba'=>'Jandouba',
                        'Kairouan'=>'Kairouan',
                        'Kebili'=>'Kebili',
                        'Mahdia'=>'Mahdia',
                        'Matmata'=>'Matmata',
                        'Mednine'=>'Mednine',
                        'Monastir'=>'Monastir',
                        'Nabeul'=>'Nabeul',
                        'Sfax'=>'Sfax',
                        'Sidi bouzid'=>'Sidi bouzid',
                        'Sousse'=>'Sousse',
                        'Tabarka'=>'Tabarka',
                        'Tataouine'=>'Tataouine',
                        'Tozeur'=>'Tozeur',
                        'Tunis'=>'Tunis',                                           
                        'Zarzis'=>'Zarzis'),
                    'expanded' => false,
                    'multiple' => false 
                    ))          
        ;
    }

    public function getName()
    {
        return 'nzo_exportateur_registration';
    }
}