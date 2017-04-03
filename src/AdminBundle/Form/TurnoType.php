<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TurnoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombreApellido',TextType::class,array('required'=>true,'label'=>'Nombre Apellido'))
            ->add('telefono',TextType::class,array('required'=>true, 'attr'=>array('data-inputmask'=>"'mask': '(999) 9999999999'",'data-mask'=>true)))
            ->add('cuit',TextType::class,array('required'=>true,'attr'=>array('data-inputmask'=>"'mask': '99-99999999-9'",'data-mask'=>true)))
            ->add('mail1',EmailType::class,array('required'=>true))
            ->add('mail2',EmailType::class,array('required'=>false))
            ->add('horaTurno',TextType::class, array('attr'  => array('class'=>"timepicker"),'required'=>true))
            ->add('tipoTramite',EntityType::class, array('class' => 'AdminBundle:TipoTramite','choice_label' => 'descripcion'));

        /**
         * TODO -> Permitir levantar tramites por sede
         */
         /*   $sede= $this->get('manager.usuario')->getSede($this->context->getToken()->getUser()->getId());
            if($sede){

                $sub =  $this->getDoctrine()->createQueryBuilder();
                $sub->select("t");
                $sub->from("AdminBundle:SedeTipoTramite","t");
                $sub->andWhere('t.sede :sedeId = AND t.tipoTramite = u.id ')->setParameter('sedeId',$sede->getId());

                $builder->add('tipoTramite',EntityType::class,
                    array('class' => 'AdminBundle:TipoTramite',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->where($er->expr()->exists($sub->getDQL()));
                        },
                        'choice_label' => 'descripcion'));
         }else{
            $builder->add('tipoTramite',EntityType::class, array('class' => 'AdminBundle:TipoTramite','choice_label' => 'descripcion'));
         }
         */
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
        return 'adminbundle_turno';
    }


}