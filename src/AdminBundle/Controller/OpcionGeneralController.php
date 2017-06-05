<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\OpcionGeneral;
use AdminBundle\Form\OpcionGeneralType;

/**
 * OpcionGeneral controller.
 *
 */
class OpcionGeneralController extends Controller
{
    /**
     * Lists all OpcionGeneral entities.
     *
     */
    public function indexAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $opcionGenerals = $em->getRepository('AdminBundle:OpcionGeneral')->findAll();

            $paginator = $this->get('knp_paginator');

            $opcionGenerals = $paginator->paginate(
                $opcionGenerals,
                $request->query->get('page', 1)/* page number */,
                10/* limit per page */
            );

            $deleteForm = $this->createDeleteForm();

        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:opciongeneral:index.html.twig', array(
            'opcionGenerals' => $opcionGenerals,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Creates a new OpcionGeneral entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $opcionGeneral = new OpcionGeneral();
            $form = $this->createForm(OpcionGeneralType::class, $opcionGeneral);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($opcionGeneral);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

                return $this->redirectToRoute('opciongeneral_index');

            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:opciongeneral:new.html.twig', array(
            'opcionGeneral' => $opcionGeneral,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a opcionGeneral entity.
     *
     */
    public function showAction(OpcionGeneral $opcionGeneral)
    {
        $deleteForm = $this->createDeleteForm($opcionGeneral);

        return $this->render('AdminBundle:opciongeneral:show.html.twig', array(
            'opcionGeneral' => $opcionGeneral,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing OpcionGeneral entity.
     *
     */
    public function editAction(Request $request, OpcionGeneral $opcionGeneral)
    {
        try {
            $deleteForm = $this->createDeleteForm($opcionGeneral);
            $editForm = $this->createForm(OpcionGeneralType::class, $opcionGeneral);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($opcionGeneral);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

                return $this->redirectToRoute('opciongeneral_edit', array('id' => $opcionGeneral->getId()));
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:opciongeneral:edit.html.twig', array(
            'opcionGeneral' => $opcionGeneral,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a OpcionGeneral entity.
     *
     */
    public function deleteAction(Request $request, OpcionGeneral $opcionGeneral)
    {
        try {
            $form = $this->createDeleteForm($opcionGeneral);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($opcionGeneral);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
                }
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->redirectToRoute('opciongeneral_index');
    }

    /**
     * Creates a form to delete a OpcionGeneral entity.
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('opciongeneral_delete', array('id' => '__obj_id__')))
            ->setMethod('DELETE')
            ->getForm();
    }
}
