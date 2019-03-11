<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Entity\Turno;
use AdminBundle\Entity\OpcionGeneral;
use FrontBundle\Form\TurnoType;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $opcionesGenerales = $this->get('manager.disponibilidad')->getOpcionesGeneralesWeb();

        $opciones = [];

        foreach ($opcionesGenerales as $key => $opcion) {
            $opciones[] = [
              'id'          => $opcion->getId(),
              'descripcion' => $opcion->getDescripcion(),
              'link'        => $this->generateUrl('seleccionar_tramite', ['opcion' => $opcion->getId()])
            ];
        }


        return $this->render('FrontBundle:Default:index.html.twig', [
          "opciones"  => json_encode($opciones, true)
        ]);
    }

    public function seleccionarTipoTramiteAction(Request $request, OpcionGeneral $opcion)
    {
        $tipoTramiteRepository = $this->getDoctrine()->getRepository('AdminBundle:TipoTramite');

        if ($opcion) {
            $tiposTramites = $this->get('manager.disponibilidad')->obtenerTipoTramiteWeb($opcion->getId());

            $helper = $this->get('vich_uploader.templating.helper.uploader_helper');

            $assetServices = $this->get('assets.packages');

            $documentos = [];

            $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl();

            foreach ($tiposTramites as $tramite) {
                $docs = [];

                foreach ($tramite->getPathFiles()  as $fileName =>$path) {
                    $fileUrl = $helper->asset($tramite, $path);

                    $docs[] = [
                      'nombre' => $fileName,
                      'link'   => $baseUrl.$assetServices->getUrl($fileUrl)
                    ];
                }

                $documentos[] = [
                    'tramite' => $tramite->getId(),
                    'documentos' => $docs
              ];
            }
        } else {
            throw new HttpException(500, "Opción inválida.");
        }

        return $this->render('FrontBundle:Default:seleccionar_tipo_tramite.html.twig', [
          'tramites'   => json_encode($tiposTramites, true),
          'documentos' => json_encode($documentos, true)
      ]);
    }

    public function seleccionarSedeAction(Request $request)
    {
        $tipoTramiteRepository = $this->getDoctrine()->getRepository('AdminBundle:TipoTramite');

        $tipoTramite = $request->get('tipoTramite');

        $tipoTramite = $tipoTramiteRepository->findOneById($tipoTramite);

        $sedeRepository = $this->getDoctrine()->getRepository('AdminBundle:Sede');

        $sedes = [];

        if ($tipoTramite) {
            $sedes = $this->get('manager.disponibilidad')->obtenerSedePorTipoTramte($tipoTramite, true);
        } else {
            throw new HttpException(500, "Tipo de trámite inválido.");
        }

        // $sedes[] = ['id'=>9,'sede'=>'otra sede','direccion'=>'asdfadsaf'];
        return $this->render('FrontBundle:Default:seleccionar_sede.html.twig', [
          'sedes' => json_encode($sedes, true),
          'tipoTramite' => $tipoTramite->getId()
        ]);
    }

    public function elegirTurnoAction(Request $request)
    {
        $tipoTramiteId = $request->get('tipoTramite');

        $sedeId = $request->get('sede');

        $tipoTramiteRepository = $this->getDoctrine()->getRepository('AdminBundle:TipoTramite');

        $tipoTramite = $tipoTramiteRepository->findOneById($tipoTramiteId);


        if ($tipoTramite) {
            if (!$tipoTramite->getSinTurno()) {
                $diasNoDisponibles = $this->get('manager.disponibilidad')->getDiasNoDisponibles($tipoTramiteId, $sedeId);

                return $this->render('FrontBundle:Default:elegir_turno.html.twig', [
                  'tipoTramite' => $tipoTramiteId,
                  'sede' => $sedeId,
                  'diasNoDisponibles' => $diasNoDisponibles
                ]);
            } else {
                $request->getSession()->set('tipoTramite', $tipoTramiteId);
                $request->getSession()->set('sede', $sedeId);

                return $this->redirectToRoute('ingreso_datos');
            }
        }
    }

    public function ingresoDatosAction(Request $request)
    {
        $session = $request->getSession();

        if (!($session->has('sede') && $session->has('tipoTramite'))) {
            return $this->redirectToRoute('front_homepage');
        }

        $turno = new Turno();

        $form = $this->createForm(TurnoType::class, $turno);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();

                $session = $request->getSession();

                $tipoTramiteId = $session->get('tipoTramite');
                $sedeId = $session->get('sede');
                $dia = $session->get('dia');
                $mes = $session->get('mes');
                $anio = $session->get('anio');
                $horario = $session->get('horario');

                $sede = $this->getDoctrine()->getRepository('AdminBundle:Sede')->find($sedeId);

                $tipoTramite = $this->getDoctrine()->getRepository('AdminBundle:TipoTramite')->find($tipoTramiteId);

                $turno->setSede($sede);

                $turno->setTipoTramite($tipoTramite);

                $turno->setHoraTurno($horario);

                if ($anio) {
                    $fechaString = "$anio-$mes-$dia";

                    $fechaTurno = new \DateTime($fechaString);

                    $turno->setFechaTurno($fechaTurno);

                }

                $turnoManager = $this->get('manager.turnos');

                $turno = $turnoManager->guardarTurno($turno);

                $hash = $turno->getComprobante()->getHash();

                $session->invalidate();


                // set flash messages
                //$this->get('session')->getFlashBag()->add('success', 'El turno se ha reservado satisfactoriamente.');

                //dump($hash);

                return $this->redirectToRoute('generar_comprobante', array('hash' => $hash));
            } catch (\Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
            }
        }

        return $this->render('FrontBundle:Default:ingreso_datos.html.twig', [
          'turno' => $turno,
          'form' => $form->createView(),
        ]);
    }

    public function generarComprobanteAction(Request $request, $hash)
    {
        $turnoManager = $this->get('manager.turnos');

        $comprobante = $turnoManager->getComprobanteByHash($hash);


        if (!$comprobante) {
            throw new HttpException(404, "No se ha podido determinar el comprobante requerido.");
        }

        $turno = $comprobante->getTurno();

        return $this->render('FrontBundle:Default:generar_comprobante.html.twig', [
          'sede' => $turno->getSede(),
          'turno' => $turno,
          'fechaImpresion' => (new \DateTime("now"))->format('d-m-Y h:i:s')
        ]);
    }


    public function cancelarTurnoAction(Request $request, String $hash)
    {
        $turnoManager = $this->get('manager.turnos');

        $comprobante = $turnoManager->getComprobanteByHash($hash);

        if (!$comprobante) {
            throw new HttpException(404, "No se ha podido determinar el comprobante requerido.");
        }

        $turno = $comprobante->getTurno();

        $cancelarForm  = $this->createCancelarForm($turno);

        return $this->render('FrontBundle:Default:cancelar_turno.html.twig', [
          'turno' => $turno,
          'cancelarForm' => $cancelarForm->createView()
        ]);
    }

    public function procesarCancelacionTurnoAction(Request $request, Turno $turno)
    {
        $cancelarForm = $this->createCancelarForm($turno);
        $cancelarForm->handleRequest($request);

        if ($cancelarForm->isSubmitted() && $cancelarForm->isValid()) {
            $turnoManager = $this->get('manager.turnos');

            $comprobante = $turnoManager->cancelarTurno($turno->getCuit(), $turno->getTurno());

            $this->get('session')->getFlashBag()->add('success', 'El turno se ha cancelado satisfactoriamente.');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'El turno no se ha podido cancelar.');
        }

        return $this->render('FrontBundle:Default:procesar_cancelacion_turno.html.twig');
    }

    /**
    * Creates a form to cancel a Turno entity.
    *
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCancelarForm(Turno $turno)
    {
        return $this->createFormBuilder()
          ->setAction($this->generateUrl('procesar_cancelacion_turno', array('id' => $turno->getId())))
          ->setMethod('POST')
          ->getForm();
    }


    public function redisAction(Request $request)
    {
        $redis = $this->container->get('snc_redis.default');

        $cola = $redis->lrange('cola', 0, -1);
        //$cola = $redis->get('cola');

        return $this->render('FrontBundle:Default:redis.html.twig', [
          'cola' => $cola
        ]);
    }

    public function agregaColaAction(Request $request)
    {
        $redis = $this->container->get('snc_redis.default');

        //Si corresponde a la primera
        $redis->rpush('NombreCola','A definir');
        //Si no corresponde hacer el insert de la siguiente forma
        //LINSERT mylist BEFORE "World" "There" para insertar "There" antes de "Word"



        return new Response('ok');
    }

    public function sacaColaAction(Request $request)
    {
        $redis = $this->container->get('snc_redis.default');

        //Controlo a que cola corresponde.
        // Si el usuario controla mas de un turnoSede.
        //controlo por el lindex cola 0 para verificar cual tiene una fecha mas antigua
        $item = $redis->lpop('cola');

        //ejemplo del push
        $sede = "sede"; //Letra de la Sede
        $turno = '';    //Numero del Turno
        $box = '';      //Nombre del Box
        $payload = [
            'channel' => $sede,
            'data' => [
                'turno' => $turno,
                'box' => $box
            ]
        ];
        $redis->publish('message', json_encode($payload));

        return new Response($item);
    }
}
