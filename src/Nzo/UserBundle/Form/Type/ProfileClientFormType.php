<?php

namespace Nzo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileClientFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
                
                ->add('nomentrop')                 
                ->add('adresse', 'textarea')  
                ->add('tel')    
                ->add('fax')  
                ->add('siteweb')  
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'nzo_client_profile';
    }
}
