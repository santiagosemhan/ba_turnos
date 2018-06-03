<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UsuarioTurnoSedeFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usuario',EntityType::class,array('class' => 'UserBundle:User','choice_label' => 'username','required'=>false,'attr'  => array('class'=>"select2")))
            ->add('turnoSede',EntityType::class,array('class' => 'AdminBundle:TurnoSede','required'=>false,'attr'  => array('class'=>"select2")))
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\UsuarioTurnoSede'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_usuarioturnosede';
    }


}