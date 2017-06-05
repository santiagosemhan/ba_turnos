<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\UsuarioTurnoSede;
use AdminBundle\Form\UsuarioTurnoSedeType;

/**
 * UsuarioTurnoSede controller.
 *
 */
class UsuarioTurnoSedeController extends Controller
{
    /**
     * Lists all UsuarioTurnoSede entities.
     *
     */
    public function indexAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $usuarioTurnoSedes = $em->getRepository('AdminBundle:UsuarioTurnoSede')->findAll();

            $paginator = $this->get('knp_paginator');

            $usuarioTurnoSedes = $paginator->paginate(
                $usuarioTurnoSedes,
                $request->query->get('page', 1)/* page number */,
                10/* limit per page */
            );

            $deleteForm = $this->createDeleteForm();
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:usuarioturnosede:index.html.twig', array(
            'usuarioTurnoSedes' => $usuarioTurnoSedes,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Creates a new UsuarioTurnoSede entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $usuarioTurnoSede = new UsuarioTurnoSede();
            $form = $this->createForm(UsuarioTurnoSedeType::class, $usuarioTurnoSede);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($usuarioTurnoSede);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

                return $this->redirectToRoute('usuarioturnosede_index');

            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:usuarioturnosede:new.html.twig', array(
            'usuarioTurnoSede' => $usuarioTurnoSede,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a usuarioTurnoSede entity.
     *
     */
    public function showAction(UsuarioTurnoSede $usuarioTurnoSede)
    {
        $deleteForm = $this->createDeleteForm($usuarioTurnoSede);

        return $this->render('AdminBundle:usuarioturnosede:show.html.twig', array(
            'usuarioTurnoSede' => $usuarioTurnoSede,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing UsuarioTurnoSede entity.
     *
     */
    public function editAction(Request $request, UsuarioTurnoSede $usuarioTurnoSede)
    {
        try {
            $deleteForm = $this->createDeleteForm($usuarioTurnoSede);
            $editForm = $this->createForm(UsuarioTurnoSedeType::class, $usuarioTurnoSede);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($usuarioTurnoSede);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

                return $this->redirectToRoute('usuarioturnosede_edit', array('id' => $usuarioTurnoSede->getId()));
            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:usuarioturnosede:edit.html.twig', array(
            'usuarioTurnoSede' => $usuarioTurnoSede,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a UsuarioTurnoSede entity.
     *
     */
    public function deleteAction(Request $request, UsuarioTurnoSede $usuarioTurnoSede)
    {
        $form = $this->createDeleteForm($usuarioTurnoSede);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($usuarioTurnoSede);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
            }
        }

        return $this->redirectToRoute('usuarioturnosede_index');
    }

    /**
     * Creates a form to delete a UsuarioTurnoSede entity.
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuarioturnosede_delete', array('id' => '__obj_id__')))
            ->setMethod('DELETE')
            ->getForm();
    }
}
