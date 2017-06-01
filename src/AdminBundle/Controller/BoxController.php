<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\Box;
use AdminBundle\Form\BoxType;

/**
 * Box controller.
 *
 */
class BoxController extends Controller
{
    /**
     * Lists all Box entities.
     *
     */

    public function indexAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $boxes = $em->getRepository('AdminBundle:Box')->findAll();

            $paginator = $this->get('knp_paginator');

            $boxes = $paginator->paginate(
                $boxes,
                $request->query->get('page', 1)/* page number */,
                10/* limit per page */
            );

            $deleteForm = $this->createDeleteForm();

        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:box:index.html.twig', array(
            'boxes' => $boxes,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Creates a new Box entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $box = new Box();
            $form = $this->createForm(BoxType::class, $box);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($box);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

                return $this->redirectToRoute('box_index');

            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:box:new.html.twig', array(
            'box' => $box,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a box entity.
     *
     */
    public function showAction(Box $box)
    {
        $deleteForm = $this->createDeleteForm($box);

        return $this->render('AdminBundle:box:show.html.twig', array(
            'box' => $box,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Box entity.
     *
     */
    public function editAction(Request $request, Box $box)
    {
        try {
            $deleteForm = $this->createDeleteForm($box);
            $editForm = $this->createForm(BoxType::class, $box);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($box);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

                return $this->redirectToRoute('box_edit', array('id' => $box->getId()));
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:box:edit.html.twig', array(
            'box' => $box,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Box entity.
     *
     */
    public function deleteAction(Request $request, Box $box)
    {
        try {
            $form = $this->createDeleteForm($box);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($box);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
                }
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->redirectToRoute('box_index');
    }

    /**
     * Creates a form to delete a Box entity.
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('box_delete', array('id' => '__obj_id__')))
            ->setMethod('DELETE')
            ->getForm();
    }
}
