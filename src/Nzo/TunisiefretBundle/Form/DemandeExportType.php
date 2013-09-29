<?php

namespace Nzo\TunisiefretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DemandeExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)

    {
	    $builder
               ->add('titre')  
                ->add('description')    
                ->add('pays', 'country', array('preferred_choices' => array('TN') ) )        
                ->add('ville')
                ->add('codepostal')
                ->add('adresse')
                ->add('datemax', 'date', array(
                                        'widget' => 'single_text',
                                        ))         
                ->add('prix')   
                ->add('demandeexporttype')   
                ->add('type', 'choice', array(                   
                    'choices' => array(
                        'Import'=>'Import',                                           
                        'Export'=>'Export'),
                    'expanded' => false,
                    'multiple' => false 
                    ))              
            ;            
           
    }

    public function getName()
    {
        return 'nzodemandeexport';
    }
   
}