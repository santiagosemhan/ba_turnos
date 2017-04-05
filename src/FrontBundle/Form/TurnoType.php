<?php

namespace FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Beelab\Recaptcha2Bundle\Validator\Constraints\Recaptcha2;

class TurnoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreApellido', TextType::class, array('required'=>true,'label'=>'Nombre Apellido'))
            ->add('telefono', TextType::class, array('required'=>true))
            ->add('cuit', TextType::class, array('required'=>true))
            ->add('mail1', EmailType::class, array('required'=>true))
            ->add('mail2', EmailType::class, array('required'=>false))
            ->add('captcha', RecaptchaType::class, [
                'mapped'   => false,
                //'required' => true,
                'constraints' => new Recaptcha2(),
            ]);
            // ->add('horaTurno', TextType::class, array('required'=>true))
            // ->add('tipoTramite', EntityType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\Turno'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'frontbundle_turno';
    }
}
