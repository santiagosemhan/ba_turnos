<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\Feriado;
use AdminBundle\Form\FeriadoType;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Feriado controller.
 *
 */
class FeriadoController extends Controller
{
    /**
     * Lists all Feriado entities.
     *
     */
    public function indexAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $feriados = $em->getRepository('AdminBundle:Feriado')->findAll();

            $paginator = $this->get('knp_paginator');

            $feriados = $paginator->paginate(
                $feriados,
                $request->query->get('page', 1)/* page number */,
                10/* limit per page */
            );

            $deleteForm = $this->createDeleteForm();

        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:feriado:index.html.twig', array(
            'feriados' => $feriados,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Creates a new Feriado entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $feriado = new Feriado();
            $form = $this->createForm(FeriadoType::class, $feriado);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $feriado->setFecha($this->get('manager.util')->getFechaDateTime($feriado->getFecha()));

                $em = $this->getDoctrine()->getManager();
                $em->persist($feriado);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

                return $this->redirectToRoute('feriado_index');

            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:feriado:new.html.twig', array(
            'feriado' => $feriado,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a feriado entity.
     *
     */
    public function showAction(Feriado $feriado)
    {
        $deleteForm = $this->createDeleteForm($feriado);

        return $this->render('AdminBundle:feriado:show.html.twig', array(
            'feriado' => $feriado,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Feriado entity.
     *
     */
    public function editAction(Request $request, Feriado $feriado)
    {

        try {
            $deleteForm = $this->createDeleteForm($feriado);
            $editForm = $this->createForm(FeriadoType::class, $feriado);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $feriado->setFecha($this->get('manager.util')->getFechaDateTime($feriado->getFecha()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($feriado);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

                return $this->redirectToRoute('feriado_edit', array('id' => $feriado->getId()));
            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:feriado:edit.html.twig', array(
            'feriado' => $feriado,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Feriado entity.
     *
     */
    public function deleteAction(Request $request, Feriado $feriado)
    {
        try {
            $form = $this->createDeleteForm($feriado);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($feriado);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
                }
            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->redirectToRoute('feriado_index');
    }

    /**
     * Creates a form to delete a Feriado entity.
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('feriado_delete', array('id' => '__obj_id__')))
            ->setMethod('DELETE')
            ->getForm();
    }
}
