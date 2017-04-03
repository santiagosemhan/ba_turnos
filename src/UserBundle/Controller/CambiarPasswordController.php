<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Event\UsuarioPasswordModificadoEvent;
use UserBundle\Form\CambiarPasswordType;

class CambiarPasswordController extends Controller
{
    public function cambiarPasswordAction(Request $request )
    {
        $usuario =  $this->getUser();
        if($usuario ) {


            $form = $this->createForm(CambiarPasswordType::class, $usuario);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $userManager = $this->get('fos_user.user_manager');

                $dispatcher = $this->get('event_dispatcher');

                $event = new UsuarioPasswordModificadoEvent($usuario);

                $dispatcher->dispatch(UsuarioPasswordModificadoEvent::NAME, $event);

                $userManager->updateUser($usuario);

                // set flash messages
                $this->get('session')->getFlashBag()->add('success', 'La contraseña se ha modificado satisfactoriamente.');

                return $this->redirectToRoute('admin_homepage');

            }

            return $this->render('UserBundle:CambiarPassword:cambiarPassword.html.twig', array(
                'usuario' => $usuario,
                'form' => $form->createView(),
            ));
        }else{
            return $this->redirectToRoute('dashboard');
        }
    }

}
