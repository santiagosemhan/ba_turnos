<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdminBundle\Entity\TextoMail;
use AdminBundle\Form\TextoMailType;

/**
 * TextoMail controller.
 *
 */
class TextoMailController extends Controller
{
    /**
     * Lists all TextoMail entities.
     *
     */
    public function indexAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $textoMails = $em->getRepository('AdminBundle:TextoMail')->findAll();

            $paginator = $this->get('knp_paginator');

            $textoMails = $paginator->paginate(
                $textoMails,
                $request->query->get('page', 1)/* page number */,
                10/* limit per page */
            );

            $deleteForm = $this->createDeleteForm();
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:textomail:index.html.twig', array(
            'textoMails' => $textoMails,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Creates a new TextoMail entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $textoMail = new TextoMail();
            $form = $this->createForm(TextoMailType::class, $textoMail);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($textoMail);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

                return $this->redirectToRoute('texto_mail_index');

            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:textomail:new.html.twig', array(
            'textoMail' => $textoMail,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a textoMail entity.
     *
     */
    public function showAction(TextoMail $textoMail)
    {
        $deleteForm = $this->createDeleteForm($textoMail);

        return $this->render('AdminBundle:textomail:show.html.twig', array(
            'textoMail' => $textoMail,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TextoMail entity.
     *
     */
    public function editAction(Request $request, TextoMail $textoMail)
    {
        try {
            $deleteForm = $this->createDeleteForm($textoMail);
            $editForm = $this->createForm(TextoMailType::class, $textoMail);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($textoMail);
                $em->flush();

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');

                return $this->redirectToRoute('texto_mail_edit', array('id' => $textoMail->getId()));
            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:textomail:edit.html.twig', array(
            'textoMail' => $textoMail,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a TextoMail entity.
     *
     */
    public function deleteAction(Request $request, TextoMail $textoMail)
    {
        try {
            $form = $this->createDeleteForm($textoMail);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($textoMail);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
                }
            }
        }catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->redirectToRoute('texto_mail_index');
    }

    /**
     * Creates a form to delete a TextoMail entity.
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('texto_mail_delete', array('id' => '__obj_id__')))
            ->setMethod('DELETE')
            ->getForm();
    }
}
