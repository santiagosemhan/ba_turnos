<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\Sede;
use AdminBundle\Form\SedeType;

/**
 * Sede controller.
 *
 */
class SedeController extends Controller
{
/**
    * Lists all Sede entities.
*
    */
    public function indexAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();

    $sedes = $em->getRepository('AdminBundle:Sede')->findAll();

    $paginator = $this->get('knp_paginator');

    $sedes = $paginator->paginate(
    $sedes,
    $request->query->get('page', 1)/* page number */,
    10/* limit per page */
    );

    $deleteForm = $this->createDeleteForm();

    return $this->render('AdminBundle:sede:index.html.twig', array(
        'sedes' => $sedes,
        'delete_form' => $deleteForm->createView()
    ));
}

/**
    * Creates a new Sede entity.
*
    */
    public function newAction(Request $request)
{
    $sede = new Sede();
    $form = $this->createForm(SedeType::class, $sede);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($sede);
    $em->flush();

    // set flash messages
    $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

    return $this->redirectToRoute('sede_index');

    }

    return $this->render('AdminBundle:sede:new.html.twig', array(
    'sede' => $sede,
    'form' => $form->createView(),
    ));
}

    /**
     * Finds and displays a sede entity.
     *
     */
    public function showAction(Sede $sede)
    {
        $deleteForm = $this->createDeleteForm($sede);

        return $this->render('AdminBundle:sede:show.html.twig', array(
            'sede' => $sede,
            'delete_form' => $deleteForm->createView(),
        ));
    }

/**
    * Displays a form to edit an existing Sede entity.
*
    */
    public function editAction(Request $request, Sede $sede)
{
    $deleteForm = $this->createDeleteForm($sede);
    $editForm = $this->createForm(SedeType::class, $sede);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($sede);
    $em->flush();

    // set flash messages
    $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

    return $this->redirectToRoute('sede_edit', array('id' => $sede->getId()));
    }

    return $this->render('AdminBundle:sede:edit.html.twig', array(
    'sede' => $sede,
    'edit_form' => $editForm->createView(),
    'delete_form' => $deleteForm->createView(),
    ));
}

/**
    * Deletes a Sede entity.
*
    */
    public function deleteAction(Request $request, Sede $sede)
{
    $form = $this->createDeleteForm($sede);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    try{
    $em = $this->getDoctrine()->getManager();
    $em->remove($sede);
    $em->flush();

    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
    }catch(\Exception $e){
    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
    }
    }

    return $this->redirectToRoute('sede_index');
}

    /**
    * Creates a form to delete a Sede entity.
    *
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createDeleteForm()
    {
    return $this->createFormBuilder()
    ->setAction($this->generateUrl('sede_delete', array('id' => '__obj_id__')))
    ->setMethod('DELETE')
    ->getForm()
    ;
    }
}
