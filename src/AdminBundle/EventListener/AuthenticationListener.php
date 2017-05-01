<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 25/04/2017
 * Time: 21:30
 */

namespace AdminBundle\EventListener;

use AdminBundle\Entity\Login;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuthenticationListener
{

    protected $container;



    public function setServiceContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

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

        try{

            $em =  $this->container->get('doctrine')->getManager();
            $user = $this->container->get('security.token_storage')->getToken()->getUser();

            $login = New Login();
            $login->setUsuario($user);
            $login->setFecha(new \DateTime("now"));
            $login->setSede($user->getUsuarioSede()->getSede());
            $login->setIp($_SERVER["REMOTE_ADDR"]);
            //$login->setNombrePc(gethostname());

            $em->persist($login);
            $em->flush();

        }catch (Exception $e){
            // set flash messages
            $this->container->get('session')->getFlashBag()->add('error', 'Hubo un error al intentar ingresar.');
        }

    }
}
