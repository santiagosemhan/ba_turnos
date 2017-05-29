<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\TipoTramite;
use AdminBundle\Form\TipoTramiteType;

/**
 * TipoTramite controller.
 *
 */
class TipoTramiteController extends Controller
{
/**
    * Lists all TipoTramite entities.
*
    */
    public function indexAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();

    $tipoTramites = $em->getRepository('AdminBundle:TipoTramite')->findAll();

    $paginator = $this->get('knp_paginator');

    $tipoTramites = $paginator->paginate(
    $tipoTramites,
    $request->query->get('page', 1)/* page number */,
    10/* limit per page */
    );

    $deleteForm = $this->createDeleteForm();

    return $this->render('AdminBundle:tipotramite:index.html.twig', array(
        'tipoTramites' => $tipoTramites,
        'delete_form' => $deleteForm->createView()
    ));
}

/**
    * Creates a new TipoTramite entity.
*
    */
    public function newAction(Request $request)
{
    $tipoTramite = new TipoTramite();
    $form = $this->createForm(TipoTramiteType::class, $tipoTramite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($tipoTramite);
        $em->flush();

        // set flash messages
        $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

        return $this->redirectToRoute('tipotramite_index');

    }

    return $this->render('AdminBundle:tipotramite:new.html.twig', array(
    'tipoTramite' => $tipoTramite,
    'form' => $form->createView(),
    ));
}

    /**
     * Finds and displays a tipoTramite entity.
     *
     */
    public function showAction(TipoTramite $tipoTramite)
    {
        $deleteForm = $this->createDeleteForm($tipoTramite);

        return $this->render('AdminBundle:tipotramite:show.html.twig', array(
            'tipoTramite' => $tipoTramite,
            'delete_form' => $deleteForm->createView(),
        ));
    }

/**
    * Displays a form to edit an existing TipoTramite entity.
*
    */
    public function editAction(Request $request, TipoTramite $tipoTramite)
{
    $deleteForm = $this->createDeleteForm($tipoTramite);
    $editForm = $this->createForm(TipoTramiteType::class, $tipoTramite);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($tipoTramite);
        $em->flush();



        // set flash messages
        $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

        return $this->redirectToRoute('tipotramite_edit', array('id' => $tipoTramite->getId()));
    }

    return $this->render('AdminBundle:tipotramite:edit.html.twig', array(
    'tipoTramite' => $tipoTramite,
    'edit_form' => $editForm->createView(),
    'delete_form' => $deleteForm->createView(),
    ));
}

/**
    * Deletes a TipoTramite entity.
*
    */
    public function deleteAction(Request $request, TipoTramite $tipoTramite)
{
    $form = $this->createDeleteForm($tipoTramite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    try{
        $em = $this->getDoctrine()->getManager();
        $em->remove($tipoTramite);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
    }catch(\Exception $e){
    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
    }
    }

    return $this->redirectToRoute('tipotramite_index');
}

    /**
    * Creates a form to delete a TipoTramite entity.
    *
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createDeleteForm()
    {
    return $this->createFormBuilder()
    ->setAction($this->generateUrl('tipotramite_delete', array('id' => '__obj_id__')))
    ->setMethod('DELETE')
    ->getForm()
    ;
    }
}
