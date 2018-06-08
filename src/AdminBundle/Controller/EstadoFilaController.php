<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdminBundle\Entity\Sede;

class EstadoFilaController extends Controller
{
    public function visualizarAction(Request $request)
    {
        $sede = $this->get('manager.usuario')->getSede($this->getUser()->getId(),$this->get('session')->get('sede'));

        try {
            $redis = $this->container->get('snc_redis.default');
        } catch (\Exception $e) {
            throw new \Exception('Error 1000.TBC.STL No se encuentra el servicio para manejar las filas');
        }

        $resultArray = array();
        $resultArrayPrioritario = array();
        try {

            $turnoSede = $this->get('manager.usuario')->getTurnoSede($this->getUser()->getId());
            foreach ($turnoSede as $turnoS) {

                $this->getDoctrine()->getManager()->refresh($turnoS);
                $turnosTipoTramites = $turnoS->getTurnoTipoTramite();

                $tramite = array();
                $letra = null;
                foreach ($turnosTipoTramites as $turnoTipoTramite) {
                    $tipoTramiteObj = $turnoTipoTramite->getTipoTramite();
                    if (is_null($letra)) {
                        $letra = $this->get('manager.turnos')->obtenerLetraTurnoSede($turnoS, $tipoTramiteObj->getId(), true);
                    }
                    $tramite[] = $tipoTramiteObj->getOpcionGeneral()->getDescripcion() . ' - ' . $tipoTramiteObj->getDescripcion();
                }

                $usuarios = array();
                $usuariosTurnosSedes = $turnoS->getUsuarioTurnoSede();
                foreach ($usuariosTurnosSedes as $usuarioTurnosSede) {
                    $usuarioObj = $usuarioTurnosSede->getUsuario();
                    $usuarios[] = $usuarioObj->getUsername();
                }


                $nombreLista = $turnoS->getSede()->getLetra() . '/' . $turnoS->getId() . '/Prioritario';
                $result = $redis->lRange($nombreLista, '0', '-1');
                $arrayA = array();
                foreach ($result as $item) {
                    $explodeItem = explode('/', $item);
                    $string = ($explodeItem[3]) . ' - ' . ($explodeItem[4]);
                    $arrayA[] = $string;
                }
                $resultArrayPrioritario[] = [
                    'lista' => 'Id: ' . $turnoS->getId() . ' ' . $turnoS->__toString() . '| Prioritario',
                    'cantidad' => count($result),
                    'items' => $arrayA,
                    'tramites' => $tramite,
                    'user' => $usuarios,
                    'letra' => $letra
                ];

                $tramite = array();
                $letra = null;

                foreach ($turnosTipoTramites as $turnoTipoTramite) {
                    $tipoTramiteObj = $turnoTipoTramite->getTipoTramite();
                    if (is_null($letra)) {
                        $letra = $this->get('manager.turnos')->obtenerLetraTurnoSede($turnoS, $tipoTramiteObj->getId(), false);
                    }
                    $tramite[] = $tipoTramiteObj->getOpcionGeneral()->getDescripcion() . ' - ' . $tipoTramiteObj->getDescripcion();
                }

                $nombreLista = $turnoS->getSede()->getLetra() . '/' . $turnoS->getId();
                $result = $redis->lRange($nombreLista, '0', '-1');
                $arrayA = array();

                $date = new \DateTime();

                foreach ($result as $item) {
                    $explodeItem = explode('/', $item);
                    if (isset($explodeItem[3])) {
                        $hora = substr($explodeItem[1],0,2).':'.substr($explodeItem[1],2,2);
                        $string = ($explodeItem[3]) . ' - ' . $explodeItem[4].' - '.$hora;
                    } else {
                        $string = '';
                    }
                    $arrayA[] = $string;
                }

                $resultArray[] = [
                    'lista' => 'Id: ' . $turnoS->getId() . ' ' . $turnoS->__toString(),
                    'cantidad' => count($result),
                    'items' => $arrayA,
                    'tramites' => $tramite,
                    'user' => $usuarios,
                    'letra' => $letra
                ];
            }


        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }
        return $this->render('AdminBundle:estadofila:visualizacion.html.twig', array(
            'sede' => $sede,
            'resultNormal' => $resultArray,
            'resultPrioritario' => $resultArrayPrioritario
        ));
    }

}