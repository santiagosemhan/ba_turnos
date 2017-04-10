<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\TurnoTramite;
use AdminBundle\Form\TurnoTramiteType;

/**
 * TurnoTramite controller.
 *
 */
class TurnoTramiteController extends Controller
{
/**
    * Lists all TurnoTramite entities.
*
    */
    public function indexAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();

    $turnoTramites = $em->getRepository('AdminBundle:TurnoTramite')->findAll();

    $paginator = $this->get('knp_paginator');

    $turnoTramites = $paginator->paginate(
    $turnoTramites,
    $request->query->get('page', 1)/* page number */,
    10/* limit per page */
    );

    $deleteForm = $this->createDeleteForm();

    return $this->render('AdminBundle:turnotramite:index.html.twig', array(
        'turnoTramites' => $turnoTramites,
        'delete_form' => $deleteForm->createView()
    ));
}

/**
    * Creates a new TurnoTramite entity.
*
    */
    public function newAction(Request $request)
{
    $turnoTramite = new TurnoTramite();
    $form = $this->createForm(TurnoTramiteType::class, $turnoTramite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($turnoTramite);
    $em->flush();

    // set flash messages
    $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

    return $this->redirectToRoute('turnotramite_index');

    }

    return $this->render('AdminBundle:turnotramite:new.html.twig', array(
    'turnoTramite' => $turnoTramite,
    'form' => $form->createView(),
    ));
}

    /**
     * Finds and displays a turnoTramite entity.
     *
     */
    public function showAction(TurnoTramite $turnoTramite)
    {
        $deleteForm = $this->createDeleteForm($turnoTramite);

        return $this->render('AdminBundle:turnotramite:show.html.twig', array(
            'turnoTramite' => $turnoTramite,
            'delete_form' => $deleteForm->createView(),
        ));
    }

/**
    * Displays a form to edit an existing TurnoTramite entity.
*
    */
    public function editAction(Request $request, TurnoTramite $turnoTramite)
{
    $deleteForm = $this->createDeleteForm($turnoTramite);
    $editForm = $this->createForm(TurnoTramiteType::class, $turnoTramite);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($turnoTramite);
    $em->flush();

    // set flash messages
    $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

    return $this->redirectToRoute('turnotramite_edit', array('id' => $turnoTramite->getId()));
    }

    return $this->render('AdminBundle:turnotramite:edit.html.twig', array(
    'turnoTramite' => $turnoTramite,
    'edit_form' => $editForm->createView(),
    'delete_form' => $deleteForm->createView(),
    ));
}

/**
    * Deletes a TurnoTramite entity.
*
    */
    public function deleteAction(Request $request, TurnoTramite $turnoTramite)
{
    $form = $this->createDeleteForm($turnoTramite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    try{
    $em = $this->getDoctrine()->getManager();
    $em->remove($turnoTramite);
    $em->flush();

    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
    }catch(\Exception $e){
    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
    }
    }

    return $this->redirectToRoute('turnotramite_index');
}

    /**
    * Creates a form to delete a TurnoTramite entity.
    *
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createDeleteForm()
    {
    return $this->createFormBuilder()
    ->setAction($this->generateUrl('turnotramite_delete', array('id' => '__obj_id__')))
    ->setMethod('DELETE')
    ->getForm()
    ;
    }
}
