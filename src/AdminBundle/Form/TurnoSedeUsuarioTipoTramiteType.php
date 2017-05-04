<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 22/04/2017
 * Time: 17:06
 */

namespace AdminBundle\Form;

use AdminBundle\Entity\TipoTramite;
use AdminBundle\Entity\TurnoTipoTramite;
use AdminBundle\Entity\UsuarioTurnoSede;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TurnoSedeUsuarioTipoTramiteType extends AbstractType
{
    private $em;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayTipoTramites = $this->getChoiseTurnoTipoTramite($options);
        $arrayTipoTramitesSeleccionados =  $this->getChoiseDataTurnoTipoTramite($options);

        $arrayUsuarioTurnoSede = $this->getChoiseUsuarioTurnoSede($options);
        $arrayUsuarioTurnoSedeSeleccionados =  $this->getChoiseDataUsuarioTurnoSede($options);

        unset($options['compound']['em']);
        $builder
            ->add('turnoTipoTramite',
                ChoiceType::class,
                array('choices' => $arrayTipoTramites,
                          'required'=>true,
                        'multiple'=> true,
                        'data' => $arrayTipoTramitesSeleccionados,
                       'attr'  => array('class'=>"select2", 'multiple'=>"multiple")))
            ->add('usuarioTurnoSede',
                ChoiceType::class,
                array('choices' => $arrayUsuarioTurnoSede,
                    'required'=>true,
                    'multiple'=> true,
                    'data' => $arrayUsuarioTurnoSedeSeleccionados,
                    'attr'  => array('class'=>"select2", 'multiple'=>"multiple")))
        ;
    }

    private function getChoiseDataUsuarioTurnoSede($options)
    {
        $turnoSede = $options['data'];
        $usuariosTurnoPorTurno = $turnoSede->getUsuarioTurnoSede();
        $array = array();
        foreach ($usuariosTurnoPorTurno as $usuarioTurnoPorTurno) {
            $array[$usuarioTurnoPorTurno->getUsuario()->getUsername()] = $usuarioTurnoPorTurno;
        }
        return $array;
    }

    private function getChoiseUsuarioTurnoSede($options)
    {
        $this->em  = $options['compound']['em'];
        $turnoSede = $options['data'];
        $array = array();
        $repositoryTT = $this->em->getRepository('UserBundle:User')->createQueryBuilder('t')
            ->innerJoin('AdminBundle:UsuarioSede', 'us', 'WITH', 'us.usuario = t.id')
            ->where('us.activo = true')
            ->andWhere('us.sede = :sedeId')->setParameter('sedeId', $turnoSede->getSede()->getId());

        $usuariosPorSede= $repositoryTT->getQuery()->getResult();

        $usuariosTurnoPorTurno = $turnoSede->getUsuarioTurnoSede();

        foreach ($usuariosPorSede as $usuarioPorSede) {
            $noExite = true;
            $tipo = null;
            foreach ($usuariosTurnoPorTurno as $usuarioTurnoPorTurno) {
                if ($usuarioTurnoPorTurno->getUsuario()->getId() == $usuarioPorSede->getId()) {
                    $tipo = $usuarioTurnoPorTurno;
                    $noExite = false;
                }
            }
            if ($noExite) {
                $tipo = new UsuarioTurnoSede();
                $tipo->setUsuario($usuarioPorSede);
                $tipo->setTurnoSede($turnoSede);
            }

            $array[$tipo->getUsuario()->getUsername()] = $tipo;
        }
        return $array;
    }

    private function getChoiseDataTurnoTipoTramite($options)
    {
        $turnoSede = $options['data'];
        $tipostramitesPorTurno = $turnoSede->getTurnoTipoTramite();
        $array = array();
        foreach ($tipostramitesPorTurno as $tipoTramite) {
            $tipo = $tipoTramite->getTipoTramite();
            $array[$tipo->getDescripcion()] = $tipoTramite;
        }
        return $array;
    }

    private function getChoiseTurnoTipoTramite($options)
    {
        $this->em  = $options['compound']['em'];
        $turnoSede = $options['data'];
        $array = array();
        $repositoryTT = $this->em->getRepository('AdminBundle:TipoTramite')->createQueryBuilder('t')
            ->where('t.activo = true');
        $tiposTramites= $repositoryTT->getQuery()->getResult();

        $turnoSede = $options['data'];
        $tipostramitesPorTurno = $turnoSede->getTurnoTipoTramite();

        foreach ($tiposTramites as $tipoTramite) {
            $noExite = true;
            $tipo = null;
            foreach ($tipostramitesPorTurno as $tipoTramitePorTurno) {
                if ($tipoTramitePorTurno->getTipoTramite()->getId() == $tipoTramite->getId()) {
                    $tipo = $tipoTramitePorTurno;
                    $noExite = false;
                }
            }
            if ($noExite) {
                $tipo = new TurnoTipoTramite();
                $tipo->setTipoTramite($tipoTramite);
                $tipo->setTurnoSede($turnoSede);
            }
            $array[$tipoTramite->__toString()] = $tipo;
        }
        return $array;
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
        return 'adminbundle_turnotipotramite';
    }
}
