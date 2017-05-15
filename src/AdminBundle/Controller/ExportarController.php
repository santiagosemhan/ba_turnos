<?php
/**
 * Created by PhpStorm.
 * User: fernando
 * Date: 29/4/17
 * Time: 11:41
 */

namespace AdminBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportarController extends Controller
{

    public function turnosAction(Request $request)
    {
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
            ->add('estados',  ChoiceType::class,array('multiple'=> true,'attr' =>array('multiple'=>"multiple",'class'=>'form-control select2'),
                'choices'  => array(
                    'Indistinto' => -1,
                    'Sin Corfirmar' => 0,
                    'Confirmados' => 1,
                    'Confirmados Sin Turnos' => 2,
                    'Confirmados Con Turnos' => 3,
                    'Atendidos' => 4,
                    'Atendidos Sin Turnos'=> 5,
                    'Atendidos Con Turnos' => 6,
                    'Cancelados' => 8
                )))
            ->add('tipoTramite', ChoiceType::class,array( 'multiple'=> true,'attr' =>array('multiple'=>"multiple",'class'=>'form-control select2'),
                'choices'  => $tiposTramitesArray))
            ->add('fechaDesde', TextType::class, array('attr' => array('class' => "form-control pull-right datepicker", 'value' => $fechaHoy)))
            ->add('fechaHasta', TextType::class, array('attr' => array('class' => "form-control pull-right datepicker", 'value' => $fechaHoy)))
            ->add('cuit', TextType::class,array('attr'  => array('class'=>"form-control"),'required'=>false))
            ->add('nroTurno', TextType::class,array('attr'  => array('class'=>"form-control"),'required'=>false))
            ->add('sede',EntityType::class,array('class' => 'AdminBundle:Sede','choice_label' => 'Sede','multiple'=> true,'required'=>true,'attr'  => array('multiple'=>"multiple",'class'=>"select2")))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Obtengo los datos del post
            $data = $form->getData();
            //En base a lo ingresado obtengo el listado de Turnos ordenados por fecha y hora
            $turnos = $this->get('manager.turnos')->obtenerExportacion($data['sede'],$data['horaDesde'],$data['horaHasta'],$data['estados'],$data['tipoTramite'],$data['fechaDesde'],$data['fechaHasta'],$data['cuit'],$data['nroTurno']);

            // genero el objeto Excel
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            //Seteo los metas del archivo
            $phpExcelObject->getProperties()->setCreator("Sistema Turnos Online")
                ->setLastModifiedBy("Sistema Turnos Online")
                ->setTitle("Exportacion de Turnos")
                ->setSubject("Sistema Turnos Online")
                ->setDescription("Listados de Turnos.")
                ->setKeywords("turnos")
                ->setCategory("");

            //Seteo cabecera
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Sede')
                ->setCellValue('B1', 'Fecha')
                ->setCellValue('C1', 'Hora')
                ->setCellValue('D1', 'Nro Turno')
                ->setCellValue('E1', 'Nombre Apellido')
                ->setCellValue('F1', 'CUIT')
                ->setCellValue('G1', 'Estado')
                ->setCellValue('H1', 'Tipo Tramite')
                ->setCellValue('I1', 'Confirmado')
                ->setCellValue('J1', 'Prioriotario')
                ->setCellValue('K1', 'Confirmado Por')
                ->setCellValue('L1', 'Hora Confirmado')
                ->setCellValue('M1', 'Hora Atendido')
                ->setCellValue('N1', 'Atendido Por')
                ->setCellValue('O1', 'Box Atendido');


            //Seteo Cuerpo
            $rowNumber = 2;
            foreach ($turnos as $turno){
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A'.$rowNumber, $turno->getSede())
                    ->setCellValue('B'.$rowNumber, $turno->getFechaTurno())
                    ->setCellValue('C'.$rowNumber, $turno->getHoraTurno())
                    ->setCellValue('D'.$rowNumber, $turno->getNumero())
                    ->setCellValue('E'.$rowNumber, $turno->getNombreApellido())
                    ->setCellValue('F'.$rowNumber, $turno->getCuit())
                    ->setCellValue('G'.$rowNumber, $turno->getEstadoInformativo())
                    ->setCellValue('H'.$rowNumber, $turno->getTipoTramite());

                if(!is_null($turno->getColaTurno())){

                    $cola = $turno->getColaTurno();
                    $prioritario = 'No';
                    if(count($cola)> 0){
                        $cola = $cola[0];
                        if($cola->getPrioritario()){
                            $prioritario = 'Si';
                        }
                    }

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('I'.$rowNumber, 'Si')
                        ->setCellValue('J'.$rowNumber, $prioritario);
                    if($cola) {
                        $phpExcelObject->setActiveSheetIndex(0)
                            ->setCellValue('K' . $rowNumber, $cola->getCreadoPor())
                            ->setCellValue('L' . $rowNumber, $cola->getFechaCreacion())
                            ->setCellValue('M' . $rowNumber, $cola->getFechaAtendido())
                            ->setCellValue('N' . $rowNumber, $cola->getUsuarioAtendido())
                            ->setCellValue('O' . $rowNumber, $cola->getBbox());
                    }

                }else{

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('I'.$rowNumber, 'No');

                }

                $rowNumber++;
            }


            //Seteo libro1 como Turnos
            $phpExcelObject->getActiveSheet()->setTitle('Turnos');
            // coloco que sea activa el libro 'turnos'
            $phpExcelObject->setActiveSheetIndex(0);

            // Creo el writer para pasarlo al response
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
            // Creo el  response
            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            // Agrego hedears
            $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'Listado de Turnos.xls'
            );
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);

            return $response;
        }


        return $this->render('AdminBundle:exportar:turnos.html.twig', array(
            'form'                  => $form->createView()
        ));

    }


    public function sesionAction(Request $request)
    {
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

        $form = $this->createFormBuilder(array('attr'=>array('class'=>'form-admin')))
            ->add('horaDesde', TextType::class,array('attr'  => array('class'=>"form-control timepicker","value"=>$horaDesde)))
            ->add('horaHasta', TextType::class,array('attr'  => array('class'=>"form-control timepicker","value"=>$horaHasta)))
            ->add('usuarios', EntityType::class,array('class' => 'UserBundle:User','choice_label' => 'username','multiple'=> true,'required'=>true,'attr'  => array('multiple'=>"multiple",'class'=>"select2")))
            ->add('fechaDesde', TextType::class, array('attr' => array('class' => "form-control pull-right datepicker", 'value' => $fechaHoy)))
            ->add('fechaHasta', TextType::class, array('attr' => array('class' => "form-control pull-right datepicker", 'value' => $fechaHoy)))
            ->add('sede',EntityType::class,array('class' => 'AdminBundle:Sede','choice_label' => 'Sede','multiple'=> true,'required'=>true,'attr'  => array('multiple'=>"multiple",'class'=>"select2")))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Obtengo los datos del post
            $data = $form->getData();
            //En base a lo ingresado obtengo el listado de Turnos ordenados por fecha y hora
            $turnos = $this->get('manager.usuario')->obtenerExportacion($data['sede'],$data['horaDesde'],$data['horaHasta'],
                                                                       $data['usuarios'],$data['fechaDesde'],$data['fechaHasta']);

            // genero el objeto Excel
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            //Seteo los metas del archivo
            $phpExcelObject->getProperties()->setCreator("Sistema Turnos Online")
                ->setLastModifiedBy("Sistema Turnos Online")
                ->setTitle("Exportacion de Sesiones")
                ->setSubject("Sistema Turnos Online")
                ->setDescription("Listados de sesiones.")
                ->setKeywords("sesiones")
                ->setCategory("");

            //Seteo cabecera
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Sede')
                ->setCellValue('B1', 'Fecha Inicio Sesion')
                ->setCellValue('C1', 'Nombre Usuario');


            //Seteo Cuerpo
            $rowNumber = 2;
            foreach ($turnos as $turno){
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A'.$rowNumber, $turno->getSede())
                    ->setCellValue('B'.$rowNumber, $turno->getFechaCreacion())
                    ->setCellValue('C'.$rowNumber, $turno->getUsuario());

                $rowNumber++;
            }


            //Seteo libro1 como Turnos
            $phpExcelObject->getActiveSheet()->setTitle('Turnos');
            // coloco que sea activa el libro 'turnos'
            $phpExcelObject->setActiveSheetIndex(0);

            // Creo el writer para pasarlo al response
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
            // Creo el  response
            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            // Agrego hedears
            $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'Listado de Login.xls'
            );
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);

            return $response;
        }


        return $this->render('AdminBundle:exportar:sesion.html.twig', array(
            'form'                  => $form->createView()
        ));

    }

}