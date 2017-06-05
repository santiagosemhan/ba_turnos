<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\CancelacionMasiva;
use AdminBundle\Form\CancelacionMasivaType;

/**
 * CancelacionMasiva controller.
 *
 */
class CancelacionMasivaController extends Controller
{
    /**
     * Lists all CancelacionMasiva entities.
     *
     */
    public function indexAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $cancelacionMasivas = $em->getRepository('AdminBundle:CancelacionMasiva')->findAll();

            $paginator = $this->get('knp_paginator');

            $cancelacionMasivas = $paginator->paginate(
                $cancelacionMasivas,
                $request->query->get('page', 1)/* page number */,
                10/* limit per page */
            );

            $deleteForm = $this->createDeleteForm();
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:cancelacionmasiva:index.html.twig', array(
            'cancelacionMasivas' => $cancelacionMasivas,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Creates a new CancelacionMasiva entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $cancelacionMasiva = new CancelacionMasiva();
            $form = $this->createForm(CancelacionMasivaType::class, $cancelacionMasiva);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $cancelacionMasiva->setFecha($this->get('manager.util')->getFechaDateTime($cancelacionMasiva->getFecha()));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($cancelacionMasiva);
                    $em->flush();

                    $this->get('manager.turnos')->cancelarTurnsoMasiva($cancelacionMasiva);

                } catch (Exception $e) {
                    $this->em->getConnection()->rollBack();
                    $this->get('session')->getFlashBag()->add('error', 'Ocurrio el seguiente error: ' . $e->getMessage());
                    return $this->redirectToRoute('cancelacionmasiva_index');
                }

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente y se envio mail a los inscriptos');

                return $this->redirectToRoute('cancelacionmasiva_index');
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:cancelacionmasiva:new.html.twig', array(
            'cancelacionMasiva' => $cancelacionMasiva,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cancelacionMasiva entity.
     *
     */
    public function showAction(CancelacionMasiva $cancelacionMasiva)
    {
        $deleteForm = $this->createDeleteForm($cancelacionMasiva);

        return $this->render('AdminBundle:cancelacionmasiva:show.html.twig', array(
            'cancelacionMasiva' => $cancelacionMasiva,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CancelacionMasiva entity.
     *
     */
    public function editAction(Request $request, CancelacionMasiva $cancelacionMasiva)
    {
        try {
            $deleteForm = $this->createDeleteForm($cancelacionMasiva);
            $editForm = $this->createForm(CancelacionMasivaType::class, $cancelacionMasiva);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($cancelacionMasiva);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

                return $this->redirectToRoute('cancelacionmasiva_edit', array('id' => $cancelacionMasiva->getId()));
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:cancelacionmasiva:edit.html.twig', array(
            'cancelacionMasiva' => $cancelacionMasiva,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a CancelacionMasiva entity.
     *
     */
    public function deleteAction(Request $request, CancelacionMasiva $cancelacionMasiva)
    {
        try {
            $form = $this->createDeleteForm($cancelacionMasiva);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($cancelacionMasiva);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
                }
            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->redirectToRoute('cancelacionmasiva_index');
    }

    /**
     * Creates a form to delete a CancelacionMasiva entity.
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cancelacionmasiva_delete', array('id' => '__obj_id__')))
            ->setMethod('DELETE')
            ->getForm();
    }
}
