<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\Sede;
use AdminBundle\Repository\TipoTramiteRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use AdminBundle\EventListener\AddHoraTurnoFieldSubscriber;

class TurnoType extends AbstractType
{
    private $sede = null;
    private $selectedEntities;

    public function __construct($selectedEntities = null)
    {
        $this->selectedEntities = $selectedEntities;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setSede($builder->getData()->getSede());

        //$propertyPathToHoraTurno = 'horaTurno';
        //$builder->addEventSubscriber(new AddHoraTurnoFieldSubscriber($propertyPathToHoraTurno,$this->sede));

        $builder->add('nombreApellido',TextType::class,array('required'=>true,'label'=>'Nombre Apellido'))
            ->add('telefono',TextType::class,array('required'=>true, 'attr'=>array('data-inputmask'=>"'mask': '(999) 9999999999'",'data-mask'=>true)))
            ->add('cuit',TextType::class,array('required'=>false,'attr'=>array('data-inputmask'=>"'mask': '99-99999999-9'",'data-mask'=>true)))
            ->add('mail1',EmailType::class,array('required'=>false))
            ->add('mail2',EmailType::class,array('required'=>false))
            ->add('horaTurno',ChoiceType::class,array( 'attr' =>array('class'=>'select2'),
                'choices'  => $this->getChoise(),
                ))
            ->add('tipoTramite',EntityType::class,
                array(  'class' => 'AdminBundle:TipoTramite',
                        'query_builder' => function(TipoTramiteRepository $er) {
                            return $er->getTramitesPorSede($this->getSede());
                        },
                        'choice_label' => 'textoCompleto',
                        'attr'  => array('class'=>"select2"),
                        'data' => $options['tipoTramite'])

                );
    }

    private function setSede($sede){
        $this->sede = $sede;
    }

    private function getSede(){
        $this->getChoise();
        return $this->sede;
    }

    private function getChoise(){

        $horaDesde  = new \DateTime('1970-01-01 00:00:00');
        $intervalo = new \DateInterval('PT' . 1 . 'M');
        $array = array();
        $indice = 0;
        $hasta = 86401;
        $array[null] = 'Seleccione un Tipo Tramite';
        while( $indice < $hasta ){
            $array[$horaDesde->format('H:i')] = $horaDesde->format('H:i');
            $horaDesde->add($intervalo);
            $indice++;
        }
        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\Turno',
            'tipoTramite' => null,
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