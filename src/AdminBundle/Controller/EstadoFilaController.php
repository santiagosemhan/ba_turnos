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
        //try {

            $turnoSede = $this->get('manager.usuario')->getTurnoSede($this->getUser()->getId());
            foreach ($turnoSede as $turnoS) {

                $turnosTipoTramites = $turnoS->getTurnoTipoTramite();

                $tramite = array();
                foreach ($turnosTipoTramites as $turnoTipoTramite) {
                    $tipoTramiteObj = $turnoTipoTramite->getTipoTramite();
                    $tramite[] = $tipoTramiteObj->getOpcionGeneral()->getDescripcion().' - '.$tipoTramiteObj->getDescripcion();
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
                foreach($result as $item ){
                    $explodeItem = explode('/', $item);
                    $string = ($explodeItem[3]).' - '. ($explodeItem[4]);
                    $arrayA[]=$string;
                }
                $resultArray[] = [
                                    'lista' => 'Id: '.$turnoS->getId().' '.$turnoS->__toString().'| Prioritario',
                                    'cantidad'=> count($result),
                                    'items'=> $arrayA,
                                    'tramites' => $tramite,
                                    'user' => $usuarios
                                ];

                $nombreLista = $turnoS->getSede()->getLetra() . '/' . $turnoS->getId();
                $result = $redis->lRange($nombreLista, '0', '-1');
                $arrayA = array();
                foreach($result as $item ){
                    $explodeItem = explode('/', $item);
                    if(isset($explodeItem[3])) {
                        $string = ($explodeItem[3]) . ' - ' . ($explodeItem[4]);
                    }else{
                        $string='';
                    }
                    $arrayA[]=$string;
                }

                $resultArray[] = [
                    'lista' => 'Id: '.$turnoS->getId().' '.$turnoS->__toString(),
                    'cantidad'=> count($result),
                    'items'=> $arrayA,
                    'tramites' => $tramite,
                    'user' => $usuarios
                ];
            }

//        } catch (\Exception $ex) {
//            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());

//        }
        return $this->render('AdminBundle:estadofila:visualizacion.html.twig', array(
            'sede' => $sede,
            'result' => $resultArray
        ));
    }

}
