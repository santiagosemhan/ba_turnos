<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use UserBundle\Entity\User;
use UserBundle\Form\UserType;
use UserBundle\Form\CambiarPasswordType;
use UserBundle\Form\UserFilterType;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
/**
    * Lists all User entities.
*
    */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();

        if ($request->getMethod() == 'POST' || $request->getMethod() == 'GET' ) {
            $datos = $request->get('userbundle_user');
            if (isset($datos['email'])) {
                $user->setEmail($datos['email']);
            }
            if (isset($datos['username'])) {
                $user->setUsername( $datos['username']);
            }

        }

        $form = $this->createForm(UserFilterType::class, $user);
        try {
            $form->handleRequest($request);
            $users = $em->getRepository('UserBundle:User')->getAllByUser($user);
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
            $users = $em->getRepository('UserBundle:User')->findAll();
        }

        $paginator = $this->get('knp_paginator');
        $users = $paginator->paginate(
            $users,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        $deleteForm = $this->createDeleteForm();

        return $this->render('UserBundle:user:index.html.twig', array(
            'users' => $users,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

/**
    * Creates a new User entity.
*
    */
    public function newAction(Request $request)
    {

        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $userManager->updateUser($user);
                $em = $this->getDoctrine()->getManager();
                $user->addRole("ROLE_USUARIO");
                $em->persist($user);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'El registro se ha guardado satisfactoriamente.');

                return $this->redirectToRoute('usuario_index');
            }
        }
        return $this->render('UserBundle:user:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('UserBundle:user:show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

/**
    * Displays a form to edit an existing User entity.
*
    */
    public function editAction(Request $request, User $user)
    {

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->get('session')->getFlashBag()->add('success', 'El registro se ha actualizado satisfactoriamente.');
            return $this->redirectToRoute('usuario_edit', array('id' => $user->getId()));
        }

        $deleteForm = $this->createDeleteForm($user);

        return $this->render('UserBundle:user:edit.html.twig', array(
            'user' => $user,
            'edit_form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

/**
    * Deletes a User entity.
*
    */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $em = $this->getDoctrine()->getManager();
                $em->remove($user);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'El registro se ha dado de baja satisfactoriamente.');
            }catch(\Exception $e){
                $this->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar eliminar el registro.');
            }
        }
        return $this->redirectToRoute('usuario_index');
    }

    /**
    * Creates a form to delete a User entity.
    *
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuario_delete', array('id' => '__obj_id__')))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    public function cambiarPasswordAction(Request $request, User $user )
    {
        $usuario =  $user;
        if($usuario ) {

            $form = $this->createForm(CambiarPasswordType::class, $usuario);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $userManager = $this->get('fos_user.user_manager');

                $userManager->updateUser($usuario);

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'La contraseña se ha modificado satisfactoriamente.');

                return $this->redirectToRoute('usuario_edit', array('id' => $user->getId()));

            }

            return $this->render('UserBundle:CambiarPassword:cambiarPassword.html.twig', array(
                'usuario' => $usuario,
                'form' => $form->createView(),
            ));
        }else{
            return $this->redirectToRoute('usuario_edit', array('id' => $user->getId()));
        }
    }
}
