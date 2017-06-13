<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TurnoTipoTramiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('tipoTramite',EntityType::class,array('class' => 'AdminBundle:TipoTramite','required'=>true,'attr'  => array('class'=>"select2")))
                ->add('turnoSede',EntityType::class,array('class' => 'AdminBundle:TurnoSede','required'=>true,'label'=>'Agenda','attr'  => array('class'=>"select2")))
                ->add('activo');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\TurnoTipoTramite'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_turnotipotramite';
    }


}
