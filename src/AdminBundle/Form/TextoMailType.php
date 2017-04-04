<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextoMailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('accion',ChoiceType::class,array( 'attr' =>array('class'=>'form-control'),
                        'choices'  => array(
                            'Nuevo Turno' => 'nuevo',
                            'Cencelado' => 'cancelado',
                            'Cancelado Masivo' => 'cancelado_masivo',
                        )))
            ->add('asunto',TextType::class,array('required'=>true,'label'=>'Asunto'))
            ->add('texto',TextareaType::class,array('required'=>true,'attr'  => array('rows'=>"10", 'cols'=>"80")))
            ->add('activo');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\TextoMail'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_textomail';
    }


}
