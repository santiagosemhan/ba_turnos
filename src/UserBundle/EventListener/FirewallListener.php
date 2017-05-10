<?php

namespace UserBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;


class FirewallListener
{
    protected $container;

    public function __construct(ContainerInterface $container) // this is @service_container
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        //$kernel = $event->getKernel();
        //$request = $event->getRequest();


        try {


            $securityContext = $this->container->get('security.authorization_checker');

            //si el usuario está autenticado, lanzo el control especial.
            if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {

                $usuario = $this->container->get('security.token_storage')->getToken()->getUser();

                //si no es super administrador
                if (!$usuario->hasRole('ROLE_SUPER_ADMIN')) {

                    $requestStack = $this->container->get('request_stack');

                    $masterRequest = $requestStack->getMasterRequest(); // this is the call that breaks ESI

                    if ($masterRequest) {

                        $ruta = $masterRequest->attributes->get('_route');

                        //si no es la request del debugger de sf o el profiler
                        if (!preg_match("/^(_|root|usuario_acceso_denegado)/", $ruta)) {

                            $rol = $usuario->getRoles();

                            if (!$this->container->get('security.manager')->isGranted($rol, $ruta)) {

                                $url = $this->container->get('router')->generate('usuario_acceso_denegado');
                                $event->setResponse(new RedirectResponse($url));
                            }

                        }
                    }
                }
            }
        }catch( AuthenticationCredentialsNotFoundException $e){
            //hago el catch de la excepcion para que siga el flujo normal de symfony
        }
    }
}