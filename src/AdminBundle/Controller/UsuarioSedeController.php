<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\UsuarioSede;
use AdminBundle\Form\UsuarioSedeType;

/**
 * UsuarioSede controller.
 *
 */
class UsuarioSedeController extends Controller
{
/**
    * Lists all UsuarioSede entities.
*
    */
    public function indexAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();

    $usuarioSedes = $em->getRepository('AdminBundle:UsuarioSede')->findAll();

    $paginator = $this->get('knp_paginator');

    $usuarioSedes = $paginator->paginate(
    $usuarioSedes,
    $request->query->get('page', 1)/* page number */,
    10/* limit per page */
    );

    $deleteForm = $this->createDeleteForm();

    return $this->render('AdminBundle:usuariosede:index.html.twig', array(
        'usuarioSedes' => $usuarioSedes,
        'delete_form' => $deleteForm->createView()
    ));
}

/**
    * Creates a new UsuarioSede entity.
*
    */
    public function newAction(Request $request)
{
    $usuarioSede = new UsuarioSede();
    $form = $this->createForm(UsuarioSedeType::class, $usuarioSede);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($usuarioSede);
    $em->flush();

    // set flash messages
    $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

    return $this->redirectToRoute('usuariosede_index');

    }

    return $this->render('AdminBundle:usuariosede:new.html.twig', array(
    'usuarioSede' => $usuarioSede,
    'form' => $form->createView(),
    ));
}

    /**
     * Finds and displays a usuarioSede entity.
     *
     */
    public function showAction(UsuarioSede $usuarioSede)
    {
        $deleteForm = $this->createDeleteForm($usuarioSede);

        return $this->render('AdminBundle:usuariosede:show.html.twig', array(
            'usuarioSede' => $usuarioSede,
            'delete_form' => $deleteForm->createView(),
        ));
    }

/**
    * Displays a form to edit an existing UsuarioSede entity.
*
    */
    public function editAction(Request $request, UsuarioSede $usuarioSede)
{
    $deleteForm = $this->createDeleteForm($usuarioSede);
    $editForm = $this->createForm(UsuarioSedeType::class, $usuarioSede);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($usuarioSede);
    $em->flush();

    // set flash messages
    $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

    return $this->redirectToRoute('usuariosede_edit', array('id' => $usuarioSede->getId()));
    }

    return $this->render('AdminBundle:usuariosede:edit.html.twig', array(
    'usuarioSede' => $usuarioSede,
    'edit_form' => $editForm->createView(),
    'delete_form' => $deleteForm->createView(),
    ));
}

/**
    * Deletes a UsuarioSede entity.
*
    */
    public function deleteAction(Request $request, UsuarioSede $usuarioSede)
{
    $form = $this->createDeleteForm($usuarioSede);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    try{
    $em = $this->getDoctrine()->getManager();
    $em->remove($usuarioSede);
    $em->flush();

    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
    }catch(\Exception $e){
    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
    }
    }

    return $this->redirectToRoute('usuariosede_index');
}

    /**
    * Creates a form to delete a UsuarioSede entity.
    *
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createDeleteForm()
    {
    return $this->createFormBuilder()
    ->setAction($this->generateUrl('usuariosede_delete', array('id' => '__obj_id__')))
    ->setMethod('DELETE')
    ->getForm()
    ;
    }
}
