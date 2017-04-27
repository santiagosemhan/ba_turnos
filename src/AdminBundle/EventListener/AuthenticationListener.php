<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 25/04/2017
 * Time: 21:30
 */

namespace AdminBundle\EventListener;

use AdminBundle\Entity\Login;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthenticationListener
{
    /**
     * onAuthenticationFailure
     *
     * @author 	Joe Sexton <joe@webtipblog.com>
     * @param 	AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure( AuthenticationFailureEvent $event )
    {
        // executes on failed login
    }

    /**
     * onAuthenticationSuccess
     *
     * @author 	Joe Sexton <joe@webtipblog.com>
     * @param 	InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess( InteractiveLoginEvent $event )
    {
        /*
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $login = New Login();
        $login->setUsuario($user);
        $login->setFecha(new \DateTime("now"));
        $login->getSede($user->getUsuarioSede()->getSede());
        $login->setIp($_SERVER["REMOTE_ADDR"]);
        $login->setNombrePc(gethostname());

        $em =  $this->getDoctrine()->getManager();
        $em->persist($login);
        $em->flush();

        dump($login);exit;
        */
    }
}
