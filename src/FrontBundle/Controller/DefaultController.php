<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AdminBundle\Entity\Turno;

class DefaultController extends Controller
{
    const TRAMITES_CON_TURNO = 0;

    const TRAMITES_SIN_TURNO = 1;

    public function indexAction()
    {
        return $this->render('FrontBundle:Default:index.html.twig', [
          "tramites_con_turno" => self::TRAMITES_CON_TURNO,
          "tramites_sin_turno" => self::TRAMITES_SIN_TURNO
        ]);
    }

    public function seleccionarTipoTramiteAction(Request $request, $sinTurno)
    {
        $tipoTramiteRepository = $this->getDoctrine()->getRepository('AdminBundle:TipoTramite');

        if (in_array($sinTurno, [0,1])) {
            $tiposTramites = $tipoTramiteRepository->getTiposByAgrupador($sinTurno, true);
        } else {
            throw new HttpException(500, "Tipo de trámite inválido.");
        }

        return $this->render('FrontBundle:Default:seleccionar_tipo_tramite.html.twig', [
        'tramites' => json_encode($tiposTramites, true)
      ]);
    }

    public function seleccionarSedeAction(Request $request)
    {
        $tipoTramite = $request->get('tipoTramite');

        $sedeRepository = $this->getDoctrine()->getRepository('AdminBundle:Sede');

        $sedes = [];

        if ($tipoTramite) {
            $sedes = $sedeRepository->getSedesByTipoTramite($tipoTramite, true);
        }

        // $sedes[] = ['id'=>9,'sede'=>'otra sede','direccion'=>'asdfadsaf'];
        return $this->render('FrontBundle:Default:seleccionar_sede.html.twig', [
          'sedes' => json_encode($sedes, true),
          'tipoTramite' => $tipoTramite
        ]);
    }

    public function elegirTurnoAction(Request $request)
    {
        $tipoTramite = $request->get('tipoTramite');

        $sede = $request->get('sede');

        $diasNoDisponibles = $this->get('manager.disponibilidad')->getDiasNoDisponibles($tipoTramite, $sede);

        return $this->render('FrontBundle:Default:elegir_turno.html.twig', [
          'tipoTramite' => $tipoTramite,
          'sede' => $sede,
          'diasNoDisponibles' => $diasNoDisponibles
        ]);
    }

    public function ingresoDatosAction(Request $request)
    {
        $session = $request->getSession();

        $tipoTramiteId = $session->get('tipoTramite');
        $sedeId        = $session->get('sede');
        $dia           = $session->get('dia');
        $mes           = $session->get('mes');
        $anio          = $session->get('anio');
        $horario       = $session->get('horario');

        $sede = $this->getDoctrine()->getRepository('AdminBundle:Sede')->find($sedeId);

        $tipoTramite = $this->getDoctrine()->getRepository('AdminBundle:TipoTramite')->find($tipoTramiteId);

        $turno = new Turno();

        $turno->setSede($sede);

        $turno->setTipoTramite($tipoTramite);

        $turno->setHoraTurno($horario);

        $turno->setFechaTurno($fechaTurno);

        exit;
    }

    public function guardarTurno(Request $request)
    {
        $turnosManager = $this->get('manager.turnos');

        if ($form->isValid()) {
            $turno = $form->getData();

            $turno = $turnosManager->guardarTurno($turno);

            if ($turno) {
            } else {
                //muestro mendsaje de error
            }
        }
    }
}
