<?php
/**
 * Created by PhpStorm.
 * User: fernando
 * Date: 1/5/17
 * Time: 23:22
 */

namespace AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdminBundle\Entity\Turno;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TurnoBoxController extends Controller
{
    public function seleccionBoxAction(Request $request)
    {

        try {
            $sede = $this->get('manager.usuario')->getSede($this->getUser()->getId());
            if (is_null($sede)) {
                // set flash messages
                $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
                return $this->redirectToRoute('admin_homepage');
            }

            $session = new Session();
            $box = $session->get('box');
            if (!is_null($box)) {
                // set flash messages
                return $this->redirectToRoute('app_box_atencion_box');
            }

            $boxs = $sede->getBox();
            $boxArray = array();
            foreach ($boxs as $box) {
                $boxArray[$box->getDescripcion()] = $box;
            }
            $form = $this->createFormBuilder(array('attr' => array('class' => 'form-admin')))
                ->add('box', ChoiceType::class, array('attr' => array('class' => 'form-control select2'),
                    'choices' => $boxArray))
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $session = new Session();
                $session->set('box', $data['box']);
                return $this->redirectToRoute('app_box_atencion_box');
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnoBox:seleccion.html.twig', array(
            'form' => $form->createView(),
            'sede' => $sede,
        ));
    }

    public function atencionBoxAction(Request $request)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if (is_null($sede)) {
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        $session = new Session();
        $box = $session->get('box');
        if (is_null($box)) {
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder se debe seleccionar un box.');
            return $this->redirectToRoute('app_box_atencion_seleccion_box');
        }

        $turno = $session->get('turno');
        $conTurno = true;
        if (is_null($turno)) {
            $conTurno = false;
        }

        return $this->render('AdminBundle:turnoBox:administrar.html.twig', array(
            'box' => 'Administrar ' . $box,
            'sede' => $sede,
            'conTurno' => $conTurno,
            'turno' => $turno
        ));
    }

    public function obtenerProximoAction(Request $request)
    {

        try {
            $sede = $this->get('manager.usuario')->getSede($this->getUser()->getId());
            if (is_null($sede)) {
                // set flash messages
                $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
                return $this->redirectToRoute('admin_homepage');
            }

            $session = new Session();
            $box = $session->get('box');
            if (is_null($box)) {
                // set flash messages
                $this->get('session')->getFlashBag()->add('error', 'Para acceder se debe seleccionar un box.');
                return $this->redirectToRoute('app_box_atencion_seleccion_box');
            }

            $turno = $this->sacarTurnoLista($box);

            if ($turno[0]) {
                $conTurno = true;
                $turno = $turno[1];
                $session = new Session();
                $session->set('turno', $turno);
                $this->get('session')->getFlashBag()->add('success', 'Se llamo por monitor al próximo número');
            } else {
                $conTurno = false;
                $this->get('session')->getFlashBag()->add('error', $turno[1]);
                $turno = null;
            }


        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnoBox:administrar.html.twig', array(
            'box' => 'Administrar ' . $box,
            'sede' => $sede,
            'conTurno' => $conTurno,
            'turno' => $turno
        ));
    }

    public function volverLLamarAction(Request $request)
    {

        try {
            $sede = $this->get('manager.usuario')->getSede($this->getUser()->getId());
            if (is_null($sede)) {
                // set flash messages
                $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
                return $this->redirectToRoute('admin_homepage');
            }

            $session = new Session();
            $box = $session->get('box');
            if (is_null($box)) {
                // set flash messages
                $this->get('session')->getFlashBag()->add('error', 'Para acceder se debe seleccionar un box.');
                return $this->redirectToRoute('app_box_atencion_seleccion_box');
            }

            $turno = $session->get('turno');
            $conTurno = true;
            if (is_null($turno)) {
                $conTurno = false;
                $this->get('session')->getFlashBag()->add('error', 'No se encuentra el turno');
            } else {
                $this->informaCambioMonitor($turno, $box);
                $this->get('session')->getFlashBag()->add('success', 'Se volvio a llamar al turno');
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->render('AdminBundle:turnoBox:administrar.html.twig', array(
            'box' => 'Administrar ' . $box,
            'sede' => $sede,
            'conTurno' => $conTurno,
            'turno' => $turno
        ));
    }

    private function sacarTurnoLista($box)
    {

        try {
            //obtengo clase de session
            $session = new Session();

            //obtengo el redis
            try {
                $redis = $this->container->get('snc_redis.default');
            } catch (\Exception $e) {
                throw new \Exception('Error 1000.TBC.STL No se encuentra el servicio para manejar las filas');
            }

            // Si el usuario controla mas de un turnoSede.
            $turnoSede = $this->get('manager.usuario')->getTurnoSede($this->getUser());

            //Se coloca el elemento a enviar al monitor
            $item = null;
            $turnoSedeItem = null;

            if (count($turnoSede) == 1) {
                $turnoSede = $turnoSede->first();
                // controlar si existen elementos en la cola prioritarios
                $nombreLista = $turnoSede->getSede()->getLetra() . '/' . $turnoSede->getId() . '/Prioritario';
                $item = $redis->lpop($nombreLista);

                if ($item == false) {
                    $nombreLista = $turnoSede->getSede()->getLetra() . '/' . $turnoSede->getId();
                    //obtengo el ultimo elemento de la cola
                    $item = $redis->lpop($nombreLista);
                    $turnoSedeItem = $turnoSede;
                }

            } else {
                if (count($turnoSede) > 1) {

                    $nombreColaSacar = null;
                    $fechaTurnoSacar = null;
                    $prioritario = false;
                    //determino de cual cola debe buscar para sacar el turno
                    foreach ($turnoSede as $turnoS) {
                        $nombreLista = $turnoS->getSede()->getLetra() . '/' . $turnoS->getId() . '/Prioritario';

                        $result = $redis->lRange($nombreLista, '0', '0');
                        if (count($result) > 0) {
                            //obtengo los datos guardos de la lista
                            $id = explode('/', $result[0]);

                            //controlo si es el primero que saco
                            if (is_null($fechaTurnoSacar)) {
                                $nombreColaSacar = $nombreLista;
                                $fechaTurnoSacar = intval($id[1]);
                                $turnoSedeItem = $turnoS;
                            } else {
                                //Determino si el que ya saque corresponde de un horario posterior al que tengo
                                if ($fechaTurnoSacar > intval($id[1])) {
                                    $nombreColaSacar = $nombreLista;
                                    $fechaTurnoSacar = intval($id[1]);
                                    $turnoSedeItem = $turnoS;
                                }
                            }

                        } else {
                            $nombreLista = $turnoS->getSede()->getLetra() . '/' . $turnoS->getId();
                            $result = $redis->lRange($nombreLista, '0', '0');
                            if (count($result) > 0) {
                                //obtengo los datos guardos de la lista
                                $id = explode('/', $result[0]);

                                //controlo si es el primero que saco
                                if (is_null($fechaTurnoSacar)) {

                                    $nombreColaSacar = $nombreLista;
                                    $fechaTurnoSacar = intval($id[1]);
                                    $turnoSedeItem = $turnoS;
                                } else {
                                    //Determino si el que ya saque corresponde de un horario posterior al que tengo
                                    if ($fechaTurnoSacar > intval($id[1])) {
                                        $nombreColaSacar = $nombreLista;
                                        $fechaTurnoSacar = intval($id[1]);
                                        $turnoSedeItem = $turnoS;
                                    }
                                }
                            }
                        }
                    }

                    //controlo que encontre algo para sacar
                    if (!is_null($nombreColaSacar)) {
                        //obtengo el ultimo elemento de la cola
                        $item = $redis->lpop($nombreColaSacar);
                    }

                }else{
                    throw new \Exception('Error 1.TBC.STL La Sede no se encuentra asociado a un Tipo de Tramite');
                }
            }


            if (!is_null($item)) {
                //Busco el turno
                $explodeItem = explode('/', $item);
                $numeroCola = intval($explodeItem[0]);
                $idTurno = intval($explodeItem[2]);

                $turnos = $this->get('manager.turnos')->buscarTurnoPorNumeroYTurnoSede($numeroCola, $turnoSedeItem, $idTurno);
                if (count($turnos) > 0) {
                    //genero los datos para enviar al monitor
                    $turno = $turnos[0];
                    $this->informaCambioMonitor($turno, $box);
                    return array(true, $turno);
                } else {
                    return array(false, "No se controlo el Turno");
                }
            } else {
                return array(false, "Sin turnos");
            }
        } catch (\Exception $e) {
            throw $e;
        }

    }

    private function informaCambioMonitor($turno, $box)
    {
        //obtengo el redis
        try {
            $redis = $this->container->get('snc_redis.default');
        } catch (\Exception $e) {
            throw new \Exception('Error 1000.TBC.ICM No se encuentra el servicio para manejar las filas');
        }

        try {
            $sede = $turno->getSede()->getLetra(); //Letra de la Sede
            $payload = [
                'channel' => $sede,
                'data' => [
                    'turno' => $turno->getTurnoBox(),
                    'box' => $box->getDescripcion(),
                ]
            ];
        } catch (\Exception $e) {
            throw new \Exception('Error 1.TBC.ICM No se encuentra asociado al Turno la Letra de la Sede o el número del turno a llamar');
        }

        try {
            $redis->publish('nuevo_turno', json_encode($payload));
        } catch (\Exception $e) {
            throw new \Exception('Error 1000.TBC.ICM No se puede avisar al monitor la llamada del nuevo turno');
        }

    }

    public function cambiarBoxAction(Request $request){
        $session = new Session();
        $box = $session->get('box');
        if (!is_null($box)) {
            // remove flash
            $session->remove('box');
            $session->remove('turno');
            return $this->redirectToRoute('app_box_atencion_seleccion_box');
        }
    }
}
