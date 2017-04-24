<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTime;

class TurnoSedeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lunes')
                ->add('martes')
                ->add('miercoles')
                ->add('jueves')
                ->add('viernes')
                ->add('sabado')
                ->add('horaTurnosDesde',TextType::class, array('attr'  => array('class'=>"timepicker"),'required'=>true,'label'=>'Hora Desde'))
                ->add('horaTurnosHasta',TextType::class, array('attr'  => array('class'=>"timepicker"),'required'=>true,'label'=>'Hora Hasta'))
                ->add('cantidadTurnos',IntegerType::class,array('required'=>true,'label'=>'Cantidad Turnos'))
                ->add('cantidadFrecuencia',IntegerType::class,array('required'=>true,'label'=>'Cantidad Frecuencia'))
                ->add('frecunciaTurnoControl',ChoiceType::class, array( 'label'=>'Frecuencia de Turnos',
                    'choices'  => array(
                        'Minutos' => 'minutos',
                        'Horas' => 'horas'
                    ),))
                /*->add('vigenciaDesde')
                ->add('vigenciaHasta')*/
                ->add('vigenciaDesde',TextType::class, array('attr' => array('class'=>"datepicker"),'label'=>'Vigencia Desde','required'=>false))
                ->add('vigenciaHasta',TextType::class, array('attr' => array('class'=>"datepicker"),'label'=>'Vigencia Hasta','required'=>false))
                ->add('activo')
                ->add('sede',EntityType::class,array('class' => 'AdminBundle:Sede','choice_label' => 'sede','required'=>true,'attr'  => array('class'=>"select2")));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\TurnoSede'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_turnosede';
    }


}
