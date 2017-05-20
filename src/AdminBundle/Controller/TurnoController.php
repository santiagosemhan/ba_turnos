<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 22/03/2017
 * Time: 21:13
 */

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdminBundle\Entity\Turno;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Turno controller.
 *
 */
class TurnoController extends Controller
{

    /**
     * Admin all Turnos entities.
     *
     */
    public function administrarAction(Request $request)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        $fechaHoy = date("d").'/'.date("m").'/'.date("Y");
        $horaDesde = "06:00 AM";
        $horaHasta = "09:00 PM";

        if($request->getMethod() == 'POST') {
            $datos = $request->request->get('form');
            if(isset($datos['fecha'])){
                $fechaHoy = $datos['fecha'];
            }
            if(isset($datos['horaDesde'])){
                $horaDesde = $datos['horaDesde'];
            }
            if(isset($datos['horaHasta'])){
                $horaHasta = $datos['horaHasta'];
            }
        }

        $em = $this->getDoctrine()->getManager();
        $tiposTramitesArray = array();
        $tiposTramitesArray['Todos'] = 0;
        $tipos = $em->getRepository('AdminBundle:TipoTramite')->findAll();
        foreach($tipos as $tipo){
            $tiposTramitesArray[$tipo->getDescripcion()] = $tipo->getId();
        }



        $form = $this->createFormBuilder(array('attr'=>array('class'=>'form-admin')))
            ->add('horaDesde', TextType::class,array('attr'  => array('class'=>"form-control timepicker","value"=>$horaDesde)))
            ->add('horaHasta', TextType::class,array('attr'  => array('class'=>"form-control timepicker","value"=>$horaHasta)))
            ->add('estados',  ChoiceType::class,array( 'attr' =>array('class'=>'form-control select2'),
                                                        'choices'  => array(
                                                            'Sin Corfirmar' => 0,
                                                            'Confirmados' => 1,
                                                            'Confirmados Sin Turnos' => 2,
                                                            'Confirmados Con Turnos' => 3,
                                                            'Atendidos' => 4,
                                                            'Atendidos Sin Turnos'=> 5,
                                                            'Atendidos Con Turnos' => 6,
                                                            'Cancelados' => 8
                                                        )))
            ->add('tipoTramite', ChoiceType::class,array( 'attr' =>array('class'=>'form-control select2'),
                                                            'choices'  => $tiposTramitesArray))
            ->add('fecha', TextType::class, array('attr' => array('class' => "form-control pull-right datepicker", 'value' => $fechaHoy)))
            ->add('cuit', TextType::class,array('attr'  => array('class'=>"form-control"),'required'=>false))
            ->add('nroTurno', TextType::class,array('attr'  => array('class'=>"form-control"),'required'=>false))
            ->setMethod('GET')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $turno = $this->get('manager.turnos')->obtenerPorFiltro($sede->getId(),$data['horaDesde'],$data['horaHasta'],$data['estados'],$data['tipoTramite'],$data['fecha'],$data['cuit'],$data['nroTurno']);
        }else {
            $turno = $this->get('manager.turnos')->obtenerTodosSinConfimar($sede->getId(),$fechaHoy);
        }

        $paginator = $this->get('knp_paginator');

        $turno = $paginator->paginate(
            $turno,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        $optionsPaginate2 = array(
            'pageParameterName' => 'page-confirmados',
            'sortFieldParameterName' => 'sort',
            'sortDirectionParameterName' => 'direction',
            'filterFieldParameterName' => 'filterParam',
            'filterValueParameterName' => 'filterValue',
            'distinct' => true
        );
        $turnosConfirmadosList = $this->get('manager.turnos')->obtenerPorFiltro($sede->getId(),'06:00 AM','09:00 PM',7/*Confirmados y no Atendido*/,0/*Todos los tramites*/,$fechaHoy);
        $turnosConfirmadosList= $paginator->paginate(
            $turnosConfirmadosList,
            $request->query->get('page-confirmados', 1)/* page number */,
            10/* limit per page */,
            $optionsPaginate2
        );


        $optionsPaginate3 = array(
            'pageParameterName' => 'page-atendidos',
            'sortFieldParameterName' => 'sort',
            'sortDirectionParameterName' => 'direction',
            'filterFieldParameterName' => 'filterParam',
            'filterValueParameterName' => 'filterValue',
            'distinct' => true
        );
        $turnosAtendidosList = $this->get('manager.turnos')->obtenerPorFiltro($sede->getId(),'06:00 AM','09:00 PM',4/*Atendidos*/,0/*Todos los tramites*/,$fechaHoy);
        $turnosAtendidosList= $paginator->paginate(
            $turnosAtendidosList,
            $request->query->get('page-atendidos', 1)/* page number */,
            10/* limit per page */,
            $optionsPaginate3
        );


        return $this->render('AdminBundle:turno:administrar.html.twig', array(
            'form'                  => $form->createView(),
            'sede'                  => $sede,
            'paginationTurnos'      => $turno,
            'turnosDia'             => $this->get('manager.turnos')->getCantidad($sede->getId(),$fechaHoy),
            'turnosConfirmados'     => $this->get('manager.turnos')->getCantidadConfirmados($sede->getId(),$fechaHoy),
            'parametriaTurnos'      => '15 turnos por hora',
            'sinTurno'              => $this->get('manager.turnos')->getCantidadSinTurnos($sede->getId(),$fechaHoy),
            'fechaHoy'              => $fechaHoy,
            'turnosConfirmadosList' => $turnosConfirmadosList,
            'turnosAtendidosList'   => $turnosAtendidosList
        ));
    }

    public function procesarCancelarAction(Request $request, Turno $turno)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }
        $this->get('manager.turnos')->cancelarTurno($turno->getCuit(),$sede->getLetra().$turno->getNumero(),true);
        return $this->redirectToRoute('turno_cancelar', array('id' => $turno->getId()));

    }

    public function cancelarAction(Request $request, Turno $turno)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        return $this->render('AdminBundle:turno:cancelar.html.twig', array(
            'turno' => $turno
        ));
    }

    /**
     * Creates a new turno entity.
     *
     */
    public function newAction(Request $request)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        $turno = new Turno();
        $form = $this->createForm('AdminBundle\Form\TurnoType', $turno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $turno->setSede($sede);
            $turno->setFechaTurno(new \DateTime("now"));
            $turno->setViaMostrador(true);
            $turno->setNumero( $this->get('manager.turnos')->obtenerProximoTurnoSede($sede->getId()) );

            $hora = (substr($turno->getHoraTurno(),0,2)); $min = (substr($turno->getHoraTurno(),3,2));
            if(substr($turno->getHoraTurno(),6,2) == 'PM'){ $hora = $hora +12; }
            $turno->setHoraTurno( new \DateTime($hora.':'.$min.':00'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($turno);

            $comprobante = new Comprobante();
            $comprobante->setTurnoId($turno);
            $comprobante->setSede($turno->getSede()->getSede());
            $comprobante->setLetra($turno->getSede()->getLetra());
            $comprobante->setNumero($turno->getNumero());
            $comprobante->setTipoTramite($turno->getTipoTramite()->getDescripcion());
            $em->persist($comprobante);

            $em->flush();

            $this->get('manager.turnos')->confirmarTurno($turno,$this->getUser(),false);

            return $this->redirectToRoute('turno_show', array('id' => $turno->getId()));
        }

        return $this->render('AdminBundle:turno:new.html.twig', array(
            'turno' => $turno,
            'form' => $form->createView(),
            'titulo' => 'Nuevo Turno',
        ));
    }

    /**
     * Creates a new turno Prioritario entity.
     *
     */
    public function newPrioritarioAction(Request $request)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        $turno = new Turno();
        $form = $this->createForm('AdminBundle\Form\TurnoType', $turno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                $turno->setSede($sede);
                $turno->setFechaTurno(new \DateTime("now"));
                $turno->setViaMostrador(true);
                $turno->setNumero( $this->get('manager.turnos')->obtenerProximoTurnoSede($sede->getId()) );
                $turno->setHoraTurno($this->get('manager.util')->getHoraDateTime($turno->getHoraTurno()));
                $em->persist($turno);

                $comprobante = new Comprobante();
                $comprobante->setTurnoId($turno);
                $comprobante->setSede($turno->getSede()->getSede());
                $comprobante->setLetra($turno->getSede()->getLetra());
                $comprobante->setNumero($turno->getNumero());
                $comprobante->setTipoTramite($turno->getTipoTramite()->getDescripcion());
                $em->persist($comprobante);

                $em->flush();

                $this->get('manager.turnos')->confirmarTurno($turno,$this->getUser(),true);

                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }


            return $this->redirectToRoute('turno_show', array('id' => $turno->getId()));
        }

        return $this->render('AdminBundle:turno:new.html.twig', array(
            'turno' => $turno,
            'form' => $form->createView(),
            'titulo' => 'Nuevo Turno Prioritario',
        ));
    }

    /**
     * Creates a new turno Prioritario entity.
     *
     */
    public function confirmarTurnoAction(Turno $turno)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }
        try{
            $this->get('manager.turnos')->confirmarTurno($turno,$this->getUser(),false);
            $this->agregarTurnoLista($turno,false);

            return $this->redirectToRoute('turno_show', array('id' => $turno->getId()));
        }catch (\Exception $e){
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            return $this->redirectToRoute('turno_show', array('id' => $turno->getId()));
        }

    }

    /**
     * Finds and displays a turno entity.
     *
     */
    public function showAction(Turno $turno)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        $titulo = "Confirmar Turno";
        $prioritario = false;

        return $this->render('AdminBundle:turno:show.html.twig', array(
            'turno' => $turno,
            'titulo' => $titulo,
            'priotitario' =>$prioritario,
        ));
    }

    public function showPrioritarioAction(Turno $turno)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        $titulo = "Confirmar Turno Prioritario";
        $prioritario = true;

        return $this->render('AdminBundle:turno:show.html.twig', array(
            'turno' => $turno,
            'titulo' => $titulo,
            'priotitario' =>$prioritario,
        ));
    }

    public function confirmarTurnoPrioritarioAction(Turno $turno)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }
        try{
            $this->get('manager.turnos')->confirmarTurno($turno,$this->getUser(),true);
            $this->agregarTurnoLista($turno,true);

            return $this->redirectToRoute('turno_show', array('id' => $turno->getId()));
        }catch (\Exception $e){
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            return $this->redirectToRoute('admin_homepage');
        }


    }

    public function imprimirAction(Turno $turno)
    {
        $sede = $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if (is_null($sede)) {
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        return $this->render('AdminBundle:turno:imprimir.html.twig', array(
            'turno' => $turno,
            'sede'  => $sede,
            'fechaImpresion' => 'Fecha Impresión: '.date("d/m/Y H:i:s"),
        ));
    }

    private function agregarTurnoLista($turno,$prioritario){

        //genero el nombre de la lista
        if($prioritario){
            $nombreLista = $turno->getTurnoSede()->getSede()->getLetra().'/'.$turno->getTurnoSede()->getId().'/Prioritario';
        }else{
            $nombreLista = $turno->getTurnoSede()->getSede()->getLetra().'/'.$turno->getTurnoSede()->getId();
        }
        //creo el formato del texto a guardar
        $cola=$turno->getColaTurno()->first();
        $formatoLiso = $cola->getNumero().'/'.$turno->getHoraTurno()->getTimestamp().'/'.$turno->getId();

        //obtengo la clase redis
        $redis = $this->get('snc_redis.default');

        //verfica si es el primer turno de la cola
        if($this->get('manager.turnos')->primerTurno($turno)){
            $this->borrarListaTurnoSede($turno);
            $redis->rpush($nombreLista,$formatoLiso);
        }else {
            //obtengo todos los resultados de la lista
            $result = $redis->lRange($nombreLista,'0','-1');

            //utilizado para determinar desde de que elemento se inserta
            $indiceInsert = null;
            foreach($result as $lista){
                $id = explode('/',$lista);
                $id = intval($id[0]);
                if($id > $cola->getNumero()){
                    //Determino si el valor anterior es el primero
                    if(is_null($indiceInsert)){
                        $indiceInsert = $lista;
                        break;
                    }
                }
            }

            //determino si tiene algun elemento en la lista
            if(count($result)> 0 ){
                //determino si corresponde al ultimo elemento de la lista
                if(is_null($indiceInsert)){
                    //coloco en el ultimo lugar de la lista
                    $redis->rpush($nombreLista, $formatoLiso);
                }else{
                    //Inserto en el primer lugar que el valor es mayor al indice a guardar.
                    //Ej.LINSERT mylist BEFORE "World" "There" para insertar "There" antes de "Word"
                    $redis->lInsert($nombreLista,'BEFORE',$indiceInsert,$formatoLiso);
                }
            }else {
                //coloco en el ultimo lugar de la lista
                $redis->rpush($nombreLista, $formatoLiso);
            }
        }
    }

    private function borrarListaTurnoSede($turno){
        //obtengo la clase redis
        $redis = $this->get('snc_redis.default');

        //Borro lista prioritaria
        $nombreLista = $turno->getTurnoSede()->getSede()->getLetra().'/'.$turno->getTurnoSede()->getId().'/Prioritario';
        $redis->del($nombreLista);

        //Borro lista comun
        $nombreLista = $turno->getTurnoSede()->getSede()->getLetra().'/'.$turno->getTurnoSede()->getId();
        $redis->del($nombreLista);
    }

}