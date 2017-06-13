<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use UserBundle\Services\RolesHelper;

class UserType extends AbstractType
{
    /**
     * @var RolesHelper
     */
    private $roles;

    /**
     * @param string $class The User class name
     * @param RolesHelper $roles Array or roles.
     */
    public function __construct($class, RolesHelper $roles)
    {
        //parent::__construct($class);

        $this->roles = $roles;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, array('required'=>true))
                ->add('username', TextType::class, array('required'=>true))
                ->add('roles', ChoiceType::class, array(
                    'choices' =>  $this->roles->getRoles(),
                    'required' => false,
                    'multiple'=>true
                ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_user';
    }



}
