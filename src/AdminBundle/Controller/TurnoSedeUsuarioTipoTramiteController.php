<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 22/04/2017
 * Time: 17:10
 */

namespace AdminBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\TurnoSede;
use AdminBundle\Entity\TurnoTipoTramite;
use AdminBundle\Entity\UsuarioTurnoSede;
use AdminBundle\Form\TurnoSedeUsuarioTipoTramiteType;

class TurnoSedeUsuarioTipoTramiteController extends Controller
{
    /**
     * Lists all TurnosSede entities.
     *
     */
    public function indexAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $turnoSedes = $em->getRepository('AdminBundle:TurnoSede')->findAll();

            $paginator = $this->get('knp_paginator');

            $turnoSedes = $paginator->paginate(
                $turnoSedes,
                $request->query->get('page', 1)/* page number */,
                10/* limit per page */
            );
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnosedeusuariotipotramite:index.html.twig', array(
            'turnosSedes' => $turnoSedes
        ));
    }

    /**
     * Creates a new TurnosSede entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $turnoSede = new TurnoSede();
            $form = $this->createForm(TurnoSedeType::class, $turnoSede);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if ($turnoSede->getVigenciaDesde()) {
                    $turnoSede->setVigenciaDesde($this->get('manager.util')->getFechaDateTime($turnoSede->getVigenciaDesde(), '00:00:00'));
                } else {
                    $turnoSede->setVigenciaDesde(null);
                }

                if ($turnoSede->getVigenciaHasta()) {
                    $turnoSede->setVigenciaHasta($this->get('manager.util')->getFechaDateTime($turnoSede->getVigenciaHasta(), '23:59:59'));
                } else {
                    $turnoSede->setVigenciaHasta(null);
                }

                $turnoSede->setHoraTurnosDesde($this->get('manager.util')->getHoraDateTime($turnoSede->getHoraTurnosDesde()));
                $turnoSede->setHoraTurnosHasta($this->get('manager.util')->getHoraDateTime($turnoSede->getHoraTurnosHasta()));

                $em = $this->getDoctrine()->getManager();
                $em->persist($turnoSede);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

                return $this->redirectToRoute('turnosedeusuariotipotramite_index');

            }

        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnosedeusuariotipotramite:new.html.twig', array(
            'turnosSede' => $turnoSede,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a turnosSede entity.
     *
     */
    public function showAction(TurnoSede $turnoSede)
    {

        return $this->render('AdminBundle:turnosedeusuariotipotramite:show.html.twig', array(
            'turnosSede' => $turnoSede
        ));
    }

    /**
     * Displays a form to edit an existing TurnosSede entity.
     *
     */
    public function editAction(Request $request, TurnoSede $turnoSede)
    {
        try {
            $customOptions = array();
            $array = array();
            $em = $this->getDoctrine()->getManager();
            $array['em'] = $em;
            $customOptions['compound'] = $array;
            $editForm = $this->createForm(TurnoSedeUsuarioTipoTramiteType::class, $turnoSede, $customOptions);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                try {

                    /*
                     *  Valida los TurnoTipoTramite que se sacaron
                     */
                    $positionsArray = $request->request->get('adminbundle_turnotipotramite')['turnoTipoTramite'];
                    $array = $this->getChoiseTurnoTipoTramite($turnoSede);
                    $turnosTipoTramiteGuardado = $em->getRepository('AdminBundle:TurnoTipoTramite')->findByTurnoSede($turnoSede);
                    foreach ($turnosTipoTramiteGuardado as $turnoTipoTramiteGuardado) {
                        $existe = false;
                        foreach ($positionsArray as $position) { //corresponde a lo seleccionado
                            $temp = $array[$position];
                            if ($turnoTipoTramiteGuardado->getId() == $temp->getId()) {
                                $existe = true;
                            }
                        }
                        if (!$existe) {
                            $em->remove($turnoTipoTramiteGuardado);
                            $em->flush();
                        }
                    }

                    /*
                     *  Valida los Usuario que se sacaron
                     */
                    $positionsArray = $request->request->get('adminbundle_turnotipotramite')['usuarioTurnoSede'];
                    $array = $this->getChoiseUsuarioTurnoSede($turnoSede);
                    $usuariosTurnoSedeGuardado = $em->getRepository('AdminBundle:UsuarioTurnoSede')->findByTurnoSede($turnoSede);
                    foreach ($usuariosTurnoSedeGuardado as $usuarioTurnoSedeGuardado) {
                        $existe = false;
                        foreach ($positionsArray as $position) { //corresponde a lo seleccionado
                            $temp = $array[$position];
                            if ($usuarioTurnoSedeGuardado->getId() == $temp->getId()) {
                                $existe = true;
                            }
                        }
                        if (!$existe) {
                            $em->remove($usuarioTurnoSedeGuardado);
                            $em->flush();
                        }
                    }

                    /*
                     * Guarda lo nuevo Seleccionado
                     */
                    $em->persist($turnoSede);
                    $em->flush();

                    // set flash messages
                    $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar modificar el registro.');
                }
                return $this->redirectToRoute('turnosedeusuariotipotramite_edit', array('id' => $turnoSede->getId()));
            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnosedeusuariotipotramite:edit.html.twig', array(
            'turnosSede' => $turnoSede,
            'textoSede' => $turnoSede->__toString(),
            'edit_form' => $editForm->createView()
        ));
    }

    private function getChoiseUsuarioTurnoSede($turnoSede)
    {
        $em = $this->get('doctrine')->getManager();
        $array = array();
        $repositoryTT = $em->getRepository('UserBundle:User')->createQueryBuilder('t')
            ->innerJoin('AdminBundle:UsuarioSede', 'us', 'WITH', 'us.usuario = t.id')
            ->where('us.activo = true')
            ->andWhere('us.sede = :sedeId')->setParameter('sedeId', $turnoSede->getSede()->getId())
            ->addOrderBy('t.username');

        $usuariosPorSede = $repositoryTT->getQuery()->getResult();

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

            $array[] = $tipo;
        }
        return $array;
    }

    private function getChoiseTurnoTipoTramite($turnoSede)
    {
        //Busca los TurnosTipoTramites guardados en TurnoSede y compara con los recibidos por post

        $em = $this->get('doctrine')->getManager();

        $array = array();
        $repositoryTT =
            $em->getRepository('AdminBundle:TipoTramite')
                ->createQueryBuilder('t')
                ->where('t.activo = true')
                ->addOrderBy('t.opcionGeneral')
                ->addOrderBy('t.id');
        $tiposTramites= $repositoryTT->getQuery()->getResult();

        $turnoSedeGuardado = $em->getRepository('AdminBundle:TurnoSede')->findOneById($turnoSede->getId());
        $tipostramitesPorTurno = $turnoSedeGuardado->getTurnoTipoTramite();

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

            $array[] = $tipo;
        }
        return $array;
    }
}