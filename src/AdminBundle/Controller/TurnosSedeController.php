<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\TurnosSede;
use AdminBundle\Form\TurnosSedeType;

/**
 * TurnosSede controller.
 *
 */
class TurnosSedeController extends Controller
{
/**
    * Lists all TurnosSede entities.
*
    */
    public function indexAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();

    $turnosSedes = $em->getRepository('AdminBundle:TurnosSede')->findAll();

    $paginator = $this->get('knp_paginator');

    $turnosSedes = $paginator->paginate(
    $turnosSedes,
    $request->query->get('page', 1)/* page number */,
    10/* limit per page */
    );

    $deleteForm = $this->createDeleteForm();

    return $this->render('AdminBundle:turnossede:index.html.twig', array(
        'turnosSedes' => $turnosSedes,
        'delete_form' => $deleteForm->createView()
    ));
}

/**
    * Creates a new TurnosSede entity.
*
    */
    public function newAction(Request $request)
{
    $turnosSede = new TurnosSede();
    $form = $this->createForm(TurnosSedeType::class, $turnosSede);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        if($turnosSede->getVigenciaDesde()) {
            $turnosSede->setVigenciaDesde($this->get('manager.util')->getFechaDateTime($turnosSede->getVigenciaDesde(),'00:00:00'));
        }else{
            $turnosSede->setVigenciaDesde(null);
        }

        if($turnosSede->getVigenciaHasta()) {
            $turnosSede->setVigenciaHasta($this->get('manager.util')->getFechaDateTime($turnosSede->getVigenciaHasta(),'23:59:59'));
        }else{
            $turnosSede->setVigenciaHasta(null);
        }

        $turnosSede->setHoraTurnosDesde($this->get('manager.util')->getHoraDateTime($turnosSede->getHoraTurnosDesde()));
        $turnosSede->setHoraTurnosHasta($this->get('manager.util')->getHoraDateTime($turnosSede->getHoraTurnosHasta()));

        $em = $this->getDoctrine()->getManager();
        $em->persist($turnosSede);
        $em->flush();

        // set flash messages
        $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

        return $this->redirectToRoute('turnossede_index');

    }

    return $this->render('AdminBundle:turnossede:new.html.twig', array(
    'turnosSede' => $turnosSede,
    'form' => $form->createView(),
    ));
}

    /**
     * Finds and displays a turnosSede entity.
     *
     */
    public function showAction(TurnosSede $turnosSede)
    {
        $deleteForm = $this->createDeleteForm($turnosSede);

        return $this->render('AdminBundle:turnossede:show.html.twig', array(
            'turnosSede' => $turnosSede,
            'delete_form' => $deleteForm->createView(),
        ));
    }

/**
    * Displays a form to edit an existing TurnosSede entity.
*
    */
    public function editAction(Request $request, TurnosSede $turnosSede)
{
    $deleteForm = $this->createDeleteForm($turnosSede);
    $editForm = $this->createForm(TurnosSedeType::class, $turnosSede);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
        if($turnosSede->getVigenciaDesde()) {
            $turnosSede->setVigenciaDesde($this->get('manager.util')->getFechaDateTime($turnosSede->getVigenciaDesde(),'00:00:00'));
        }else{
            $turnosSede->setVigenciaDesde(null);
        }

        if($turnosSede->getVigenciaHasta()) {
            $turnosSede->setVigenciaHasta($this->get('manager.util')->getFechaDateTime($turnosSede->getVigenciaHasta(),'23:59:59'));
        }else{
            $turnosSede->setVigenciaHasta(null);
        }

        $turnosSede->setHoraTurnosDesde($this->get('manager.util')->getHoraDateTime($turnosSede->getHoraTurnosDesde()));
        $turnosSede->setHoraTurnosHasta($this->get('manager.util')->getHoraDateTime($turnosSede->getHoraTurnosHasta()));

        $em = $this->getDoctrine()->getManager();
        $em->persist($turnosSede);
        $em->flush();

        // set flash messages
        $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

        return $this->redirectToRoute('turnossede_edit', array('id' => $turnosSede->getId()));
    }

    return $this->render('AdminBundle:turnossede:edit.html.twig', array(
    'turnosSede' => $turnosSede,
    'edit_form' => $editForm->createView(),
    'delete_form' => $deleteForm->createView(),
    ));
}

/**
    * Deletes a TurnosSede entity.
*
    */
    public function deleteAction(Request $request, TurnosSede $turnosSede)
{
    $form = $this->createDeleteForm($turnosSede);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    try{
    $em = $this->getDoctrine()->getManager();
    $em->remove($turnosSede);
    $em->flush();

    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
    }catch(\Exception $e){
    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
    }
    }

    return $this->redirectToRoute('turnossede_index');
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
    ->setAction($this->generateUrl('turnossede_delete', array('id' => '__obj_id__')))
    ->setMethod('DELETE')
    ->getForm()
    ;
    }
}
