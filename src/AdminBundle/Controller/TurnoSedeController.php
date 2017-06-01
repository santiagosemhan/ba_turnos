<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\TurnoSede;
use AdminBundle\Form\TurnoSedeType;

/**
 * TurnosSede controller.
 *
 */
class TurnoSedeController extends Controller
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

            $deleteForm = $this->createDeleteForm();
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnosede:index.html.twig', array(
            'turnosSedes' => $turnoSedes,
            'delete_form' => $deleteForm->createView()
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

                return $this->redirectToRoute('turnosede_index');

            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnosede:new.html.twig', array(
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
        $deleteForm = $this->createDeleteForm($turnoSede);

        return $this->render('AdminBundle:turnosede:show.html.twig', array(
            'turnosSede' => $turnoSede,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TurnosSede entity.
     *
     */
    public function editAction(Request $request, TurnoSede $turnoSede)
    {
        try {
            $deleteForm = $this->createDeleteForm($turnoSede);
            if ($turnoSede->getHoraTurnosDesde()) {
                $turnoSede->setHoraTurnosDesde($turnoSede->getHoraTurnosDesde()->format('h:i A'));
            }
            if ($turnoSede->getHoraTurnosHasta()) {
                $turnoSede->setHoraTurnosHasta($turnoSede->getHoraTurnosHasta()->format('h:i A'));
            }
            if ($turnoSede->getVigenciaDesde()) {
                $turnoSede->setVigenciaDesde($turnoSede->getVigenciaDesde()->format('d/m/Y'));
            }
            if ($turnoSede->getVigenciaHasta()) {
                $turnoSede->setVigenciaHasta($turnoSede->getVigenciaHasta()->format('d/m/Y'));
            }
            $editForm = $this->createForm(TurnoSedeType::class, $turnoSede);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
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
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

                return $this->redirectToRoute('turnosede_edit', array('id' => $turnoSede->getId()));
            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnosede:edit.html.twig', array(
            'turnosSede' => $turnoSede,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a TurnosSede entity.
     *
     */
    public function deleteAction(Request $request, TurnoSede $turnoSede)
    {
        $form = $this->createDeleteForm($turnoSede);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($turnoSede);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro. ' . $e);
            }
        }

        return $this->redirectToRoute('turnosede_index');
    }

    /**
     * Creates a form to delete a TurnosSede entity.
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('turnosede_delete', array('id' => '__obj_id__')))
            ->setMethod('DELETE')
            ->getForm();
    }
}
