<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TurnoTramiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cantidadTurno',IntegerType::class,array('required'=>true,'label'=>'Cantidad por Turnos'))
                ->add('cantidadSlot',IntegerType::class,array('required'=>true,'label'=>'Cantidad Turnos Contiguo'))
                ->add('tipoTramite',EntityType::class,array('class' => 'AdminBundle:TipoTramite','required'=>true))
                ->add('turnosSede',EntityType::class,array('class' => 'AdminBundle:TurnosSede','required'=>true))
                ->add('activo');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\TurnoTramite'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_turnotramite';
    }


}
