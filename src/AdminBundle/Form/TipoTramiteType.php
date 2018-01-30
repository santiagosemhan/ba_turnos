<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TipoTramiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('opcionGeneral', EntityType::class, array('class' => 'AdminBundle:OpcionGeneral','choice_label' => 'descripcion','required'=>true,'attr'  => array('class'=>"select2")))
            ->add('descripcion')
            ->add('texto', TextareaType::class, array('attr'  => array('rows'=>"10", 'cols'=>"80")))
            ->add('sinTurno')
            ->add('documento1File',
                VichFileType::class, [
                    'required' => false,
                    'allow_delete' => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                    'label' => 'Formulario1',
                ])
            ->add('documento2File',
                VichFileType::class, [
                    'required' => false,
                    'allow_delete' => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                    'label' => 'Formulario2',
                ])
            ->add('documento3File',
                VichFileType::class, [
                    'required' => false,
                    'allow_delete' => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                    'label' => 'Formulario3',
                ])
            ->add('documento4File',
                VichFileType::class, [
                    'required' => false,
                    'allow_delete' => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                    'label' => 'Formulario4',
                ])
            ->add('documento5File',
                VichFileType::class, [
                    'required' => false,
                    'allow_delete' => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                    'label' => 'Formulario5',
                ])
            ->add('documento6File',
                VichFileType::class, [
                    'required' => false,
                    'allow_delete' => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                    'label' => 'Formulario6',
                ])
            ->add('activo');

        $builder->get('documento1File')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $e) {
            if (!empty($e->getData())) {
                /** @var Application $app */
                $app = $e->getForm()->getParent()->getData();
                $app->setUpdateAt(new \DateTime());
            }
        });


        $builder->get('documento2File')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $e) {
            if (!empty($e->getData())) {
                /** @var Application $app */
                $app = $e->getForm()->getParent()->getData();
                $app->setUpdateAt(new \DateTime());
            }
        });


        $builder->get('documento3File')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $e) {
            if (!empty($e->getData())) {
                /** @var Application $app */
                $app = $e->getForm()->getParent()->getData();
                $app->setUpdateAt(new \DateTime());
            }
        });


        $builder->get('documento4File')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $e) {
            if (!empty($e->getData())) {
                /** @var Application $app */
                $app = $e->getForm()->getParent()->getData();
                $app->setUpdateAt(new \DateTime());
            }
        });


        $builder->get('documento5File')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $e) {
            if (!empty($e->getData())) {
                /** @var Application $app */
                $app = $e->getForm()->getParent()->getData();
                $app->setUpdateAt(new \DateTime());
            }
        });


        $builder->get('documento6File')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $e) {
            if (!empty($e->getData())) {
                /** @var Application $app */
                $app = $e->getForm()->getParent()->getData();
                $app->setUpdateAt(new \DateTime());
            }
        });

        /*$field = $builder->get('descripcion');   // get the field
        $options = $field->getOptions();            // get the options
        $type = $field->getType()->getName();       // get the name of the type
        $options['attr'] = array('class' => 'une_classe');          // change the label
        $builder->add('descripcion', $type, $options); // replace the field
        */
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\TipoTramite'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_tipotramite';
    }
}
