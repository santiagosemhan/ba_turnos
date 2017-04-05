<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class AjaxController extends Controller
{
    private function notFoundEntityValues($id)
    {
        return [
          'http_estado' => 404,
          'data' => ['detalle' => "La referencia al objeto $id no se ha encontrado"]
        ];
    }

    public function getTipoTramiteAction(Request $request)
    {
        $tipoTramiteId = $request->get('tipoTramiteId');

        $notFound = $this->notFoundEntityValues($tipoTramiteId);

        $tipoTramite = $this->getDoctrine()->getRepository('AdminBundle:TipoTramite')->find($tipoTramiteId);

        if ($tipoTramite) {
            return new JsonResponse($tipoTramite, 200, array('Content-Type' => 'application/json')
          );
        }

        return new JsonResponse($notFound['data'], $notFound['http_estado']);
    }

    public function getHorariosAction(Request $request)
    {
        $dia  = $request->get('dia');
        $mes  = $request->get('mes');
        $anio = $request->get('anio');
        $tipoTramite = $request->get('tipoTramite');
        $sede = $request->get('sede');

        $horasDisponibles = $this->get('manager.disponibilidad')->getHorasDisponibles($dia, $mes, $anio, $tipoTramite, $sede);

        return new JsonResponse($horasDisponibles, 200);
    }

    public function postPreReservaAction(Request $request)
    {
        $constraint = new Collection(array(
          'tipoTramite' => new NotBlank(),
          'sede'  => new NotBlank(),
          'dia' => new NotBlank(),
          'mes' => new NotBlank(),
          'anio' => new NotBlank(),
          'horario' => new NotBlank(),
        ));

        $violationList = $this->get('validator')->validate($request->request->all(), $constraint);

        if ($violationList->count()==0) {
            $session = $request->getSession();

            $session->set('tipoTramite', $request->request->get('tipoTramite'));
            $session->set('sede', $request->request->get('sede'));
            $session->set('dia', $request->request->get('dia'));
            $session->set('mes', $request->request->get('mes'));
            $session->set('anio', $request->request->get('anio'));
            $session->set('horario', $request->request->get('horario'));

            $reservaTurno = [
              'reserva' => true,
              'detail'  => 'la reserva ha sido guardada correctamente'
            ];

            return new JsonResponse($reservaTurno, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(['error' => 'la reserva no se pudo procesar correctamente'], 500);
    }
}
