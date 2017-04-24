<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\OpcionesGenerales;
use AdminBundle\Form\OpcionesGeneralesType;

/**
 * OpcionesGenerales controller.
 *
 */
class OpcionesGeneralesController extends Controller
{
/**
    * Lists all OpcionesGenerales entities.
*
    */
    public function indexAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();

    $opcionesGenerales = $em->getRepository('AdminBundle:OpcionesGenerales')->findAll();

    $paginator = $this->get('knp_paginator');

    $opcionesGenerales = $paginator->paginate(
    $opcionesGenerales,
    $request->query->get('page', 1)/* page number */,
    10/* limit per page */
    );

    $deleteForm = $this->createDeleteForm();

    return $this->render('AdminBundle:opcionesgenerales:index.html.twig', array(
        'opcionesGenerales' => $opcionesGenerales,
        'delete_form' => $deleteForm->createView()
    ));
}

/**
    * Creates a new OpcionesGenerales entity.
*
    */
    public function newAction(Request $request)
{
    $opcionesGenerale = new OpcionesGenerales();
    $form = $this->createForm(OpcionesGeneralesType::class, $opcionesGenerale);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($opcionesGenerale);
    $em->flush();

    // set flash messages
    $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

    return $this->redirectToRoute('opcionesgenerales_index');

    }

    return $this->render('AdminBundle:opcionesgenerales:new.html.twig', array(
    'opcionesGenerale' => $opcionesGenerale,
    'form' => $form->createView(),
    ));
}

    /**
     * Finds and displays a opcionesGenerale entity.
     *
     */
    public function showAction(OpcionesGenerales $opcionesGenerale)
    {
        $deleteForm = $this->createDeleteForm($opcionesGenerale);

        return $this->render('AdminBundle:opcionesgenerales:show.html.twig', array(
            'opcionesGenerale' => $opcionesGenerale,
            'delete_form' => $deleteForm->createView(),
        ));
    }

/**
    * Displays a form to edit an existing OpcionesGenerales entity.
*
    */
    public function editAction(Request $request, OpcionesGenerales $opcionesGenerale)
{
    $deleteForm = $this->createDeleteForm($opcionesGenerale);
    $editForm = $this->createForm(OpcionesGeneralesType::class, $opcionesGenerale);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($opcionesGenerale);
    $em->flush();

    // set flash messages
    $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

    return $this->redirectToRoute('opcionesgenerales_edit', array('id' => $opcionesGenerale->getId()));
    }

    return $this->render('AdminBundle:opcionesgenerales:edit.html.twig', array(
    'opcionesGenerale' => $opcionesGenerale,
    'edit_form' => $editForm->createView(),
    'delete_form' => $deleteForm->createView(),
    ));
}

/**
    * Deletes a OpcionesGenerales entity.
*
    */
    public function deleteAction(Request $request, OpcionesGenerales $opcionesGenerale)
{
    $form = $this->createDeleteForm($opcionesGenerale);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    try{
    $em = $this->getDoctrine()->getManager();
    $em->remove($opcionesGenerale);
    $em->flush();

    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
    }catch(\Exception $e){
    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
    }
    }

    return $this->redirectToRoute('opcionesgenerales_index');
}

    /**
    * Creates a form to delete a OpcionesGenerales entity.
    *
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createDeleteForm()
    {
    return $this->createFormBuilder()
    ->setAction($this->generateUrl('opcionesgenerales_delete', array('id' => '__obj_id__')))
    ->setMethod('DELETE')
    ->getForm()
    ;
    }
}
