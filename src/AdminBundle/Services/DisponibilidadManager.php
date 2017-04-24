<?php


namespace AdminBundle\Services;

use AdminBundle\Entity\Turnos;
use Doctrine\ORM\EntityManager;

class DisponibilidadManager
{

    private $em;
    private $util;
    private $mesAtincipacionTurnos;

    public function __construct(EntityManager $em, UtilManager $util )
    {
        $this->em = $em;
        $this->util = $util;
    }

    public function setMesAtincipacionTurnos($mesAtincipacionTurnos){
        $this->mesAtincipacionTurnos=$mesAtincipacionTurnos;
    }

    public function getMesAtincipacionTurnos(){
        return $this->mesAtincipacionTurnos;
    }

    public function getOpcionesGenerales($hydrate_array = false){
        $opcionesGenerales= $this->getDoctrine()->getRepository('AdminBundle:OpcionesGenerales');
        return $opcionGeneral = $opcionesGenerales->getOpcionesGenerales($hydrate_array);
    }

    public function obtenerTipoTramite($opcionGeneralId,$hydrate_array=false){
        $tiposTramites= $this->getDoctrine()->getRepository('AdminBundle:TipoTramite');
        return $opcionGeneral = $tiposTramites->getTipoTramiteByOpcionesGenerales($opcionGeneralId,$hydrate_array);
    }

    public function obtenerSedePorTipoTramte($tipoTramiteId,$hydrate_array = false){
        $tipoTramite = $this->em->getRepository('AdminBundle:TipoTramite')->findOneById($tipoTramiteId);
        $array = array();
        foreach ($tipoTramite->getTurnoTipoTramite() as $turnoTipoTramte){
            if($turnoTipoTramte){
                $turnoSede = $turnoTipoTramte->getTurnoSede();
                if($turnoSede){
                    $sede = $turnoSede->getSede();
                    if($sede){
                        if($hydrate_array){
                            $array[$sede->getId()] = array(
                                                                'id'=>$sede->getId(),
                                                                'sede'=>$sede->getSede(),
                                                                'direccion'=>$sede->getDireccion(),
                                                                'letra'=>$sede->getLetra(),
                                                            );
                        }else{
                            $array[$sede->getId()] = $sede;
                        }

                    }
                }
            }
        }
        return $array;
    }

    /**
     * Obtiene los días disponibles para turnos en base al mes/año, tipoTurno y Sede
     *
     * @param interger $mes
     * @param integer $anio
     * @param integer $tipoTurnoId
     * @param integer $sedeId
     *
     * @return array
     */
    public function getDiasNoDisponibles($tipoTramiteId,$sedeId,$mes=null,$anio=null){
        if(is_null($mes)){
            $mes = intval(date('m'));
        }
        if(is_null($anio)){
            $anio = intval(date('Y'));
        }
        $cont = 0;
        $array = array();
        while($cont < ($this->mesAtincipacionTurnos + 1) ){
            $array = $this->getDiasDisponiblesMes($mes,$anio,$tipoTramiteId,$sedeId,$array);
            if($mes<12){
                $mes++;
            }else{
                $mes = 1;
                $anio ++;
            }
            $cont++;
        }
        return $array;
    }

    public function getDiasDisponiblesMes($mes,$anio,$tipoTramiteId,$sedeId,$array){
        $diaRecorrido = 1;
        $diaHabil =array();
        $turnosDelMes = array();
        $busca =false;

        if(intval(date('m'))==$mes) {
            if (intval(date('d')) >= $diaRecorrido) {
                $diaRecorrido = intval(date('d'));
            }
            $busca =true;
        }else if($mes> intval(date('m'))) {
            $busca =true;
        }else {
            $busca =false;
        }
        if($busca){

            $primerDia = $this->util->getFechaDateTime(sprintf("%02d",$diaRecorrido) . '/' . sprintf("%02d",$mes) . '/' . $anio, '00:00:00');
            $ultimoDia = $this->util->getUltimaFechaMesDateTime($mes, $anio, '23:59:59');
            $ultimoDiaMes = intval($ultimoDia->format('d'));

            //busco la parametrización por día (teniendo en cuenta las vigencias)
            $repositoryTS = $this->em->getRepository('AdminBundle:TurnoSede')->createQueryBuilder('ts')
                ->where('ts.sede = :sedeId')->setParameter('sedeId', $sedeId);
            $turnosSede = $repositoryTS->getQuery()->getResult();
            $turnosDelMes = array();
            $existeTipoTramiteSede = false;
            $turnosSedeUtilizados = array();
            foreach ($turnosSede as $turnoSede) {
                $turnoSedeDefineTramite = false;
                if(count($turnoSede->getTurnoTipoTramite())>0){
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno){
                        if($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTramiteId){
                            $existeTipoTramiteSede = true;
                            $turnoSedeDefineTramite = true;
                        }
                    }
                    if($turnoSedeDefineTramite){
                        $turnosDelMes = $this->getCantidadDiaTurno($tipoTramiteId,$turnoSede,$turnosDelMes,$diaRecorrido,$ultimoDiaMes,$mes,$anio);
                        $turnosSedeUtilizados[] = $turnoSede;
                    }
                }else{
                    $turnosDelMes = $this->getCantidadDiaTurno($tipoTramiteId,$turnoSede,$turnosDelMes,$diaRecorrido,$ultimoDiaMes,$mes,$anio);
                    $turnosSedeUtilizados[] = $turnoSede;
                }
            }

            //Busco y Resto por día los turnos  dados
            $repositoryT = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p')
                ->where('p.sede = :sedeId')->setParameter('sedeId', $sedeId)
                ->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                ->andWhere('p.fechaCancelado IS NULL')
                ->andWhere('p.turnoSede IN (:turnosSedes)')
                ->setParameter('fecha_turno_desde', $primerDia)
                ->setParameter('fecha_turno_hasta', $ultimoDia)
                ->setParameter('turnosSedes',$turnosSedeUtilizados);


            if($existeTipoTramiteSede){
                $repositoryT->andWhere('p.tipoTramite = :tipo_tramite')->setParameter('tipo_tramite', $tipoTramiteId);
            }
            $turnos = $repositoryT->getQuery()->getResult();


            foreach ($turnos as $turno) {
                if (isset($turnosDelMes[$turno->getFechaTurno()->format('d')])) {
                    //Controlo si el turno pase mas de un slot
                    $repositoryTT = $this->em->getRepository('AdminBundle:TurnoTipoTramite')->createQueryBuilder('tt')
                        ->innerJoin('AdminBundle:TurnoSede','ts','WITH','tt.turnosSede = ts.id')
                        ->where('(tt.tipoTramite = :tipoTramite) AND tt.activo = true')->setParameter('tipoTramite', $tipoTramiteId)
                        ->andWhere('(ts.sede = :sedeId) AND ts.activo = true ')->setParameter('sedeId', $sedeId)
                        ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')->setParameter('horaTurno',$turno->getHoraTurno());
                    $turnosTramites = $repositoryTT->getQuery()->getResult();

                    $suma = 1;
                    foreach ($turnosTramites as $turnoTramite){
                        if(!is_null($turnoTramite->getCantidadSlot())){
                            $suma = $turnoTramite->getCantidadSlot();
                        }
                    }
                    $turnosDelMes[$turno->getFechaTurno()->format('d')] = $turnosDelMes[$turno->getFechaTurno()->format('d')] - $suma;
                }
            }

            //Armo el array con los dias
            $iterator = $diaRecorrido;
            while ($iterator <= $ultimoDiaMes) {
                if (isset($turnosDelMes[$iterator])) {
                    if ($turnosDelMes[$iterator] == 0) {
                        $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> $iterator);
                    }
                }else{
                    $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> $iterator);
                }
                $iterator++;
            }

            //Feriados
            $repositoryF = $this->em->getRepository('AdminBundle:Feriado', 'f')->createQueryBuilder('f')
                ->where('( ( f.fecha between :fecha_desde  and :fecha_hasta ) OR ( f.repiteAnio = true) AND f.activo = true)')
                ->setParameter('fecha_desde', $primerDia)->setParameter('fecha_hasta', $ultimoDia);
            $feriados = $repositoryF->getQuery()->getResult();
            foreach ($feriados as $feriado){
                if($feriado->getRepiteAnio()){
                    if($feriado->getFecha()->format('m') == $mes){
                        if(is_null($feriado->getSede())){
                            if(!in_array($feriado->getFecha()->format('d'), $diaHabil)){
                                $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($feriado->getFecha()->format('d')));
                            }
                        }else{
                            if($feriado->getSede()->getId()== $sedeId){
                                if(!in_array($feriado->getFecha()->format('d'), $diaHabil)){
                                    $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($feriado->getFecha()->format('d')));
                                }
                            }
                        }
                    }
                }else{
                    if(is_null($feriado->getSede())){
                        if(!in_array($feriado->getFecha()->format('d'), $diaHabil)){
                            $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($feriado->getFecha()->format('d')));
                        }
                    }else{
                        if($feriado->getSede()->getId()== $sedeId){
                            if(!in_array($feriado->getFecha()->format('d'), $diaHabil)){
                                $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($feriado->getFecha()->format('d')));
                            }
                        }
                    }
                }
            }

            //Cancelación Masiva
            $repositoryC = $this->em->getRepository('AdminBundle:CancelacionMasiva', 'f')->createQueryBuilder('f')
                ->where(' ( ( f.fecha between :fecha_desde  and :fecha_hasta )  AND f.activo = true)')
                ->setParameter('fecha_desde', $primerDia)->setParameter('fecha_hasta', $ultimoDia);
            $cancelaciones = $repositoryC->getQuery()->getResult();
            foreach ($cancelaciones as $cancelacion){
                if($cancelacion->getSede()->getId()== $sedeId){
                    if(!in_array($cancelacion->getFecha()->format('d'), $diaHabil)){
                        $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($cancelacion->getFecha()->format('d')));
                    }
                }
            }
        }
        return $array;

    }

    private function getCantidadDiaTurno($tipoTramiteId,$turnoSede,$cantidadDiaTurno,$diaRecorrido,$ultimoDiaMes,$mes,$anio){

        //Obtengo la cantidad de horas que atienden en la sede
        $horaDesde = $turnoSede->getHoraTurnosDesde();
        $horaHasta = $turnoSede->getHoraTurnosHasta();
        $horasTurno = $horaHasta->diff($horaDesde);
        $difHoras = intval($horasTurno->format('%H'));
        $difMinutos = intval($horasTurno->format('%i'));

        //Obtengo como esta repartido los turnos
        $cantidadPorTurno = 0;
        if ($turnoSede->getFrecunciaTurnoControl() == 'minutos') {
            $difMinutos = $difMinutos + ($difHoras * 60);
            $difMinutos = ($difMinutos / $turnoSede->getCantidadFrecuencia());
            $cantidad = 0;
            if( count($turnoSede->getTurnoTipoTramite())>0 ){
                foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno){
                    if($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTramiteId){
                        if(!is_null($tipoTramiteTurno->getCantidadTurno()) OR $tipoTramiteTurno->getCantidadTurno() > 0 ){
                            $cantidad = $tipoTramiteTurno->getCantidadTurno();
                        }else{
                            $cantidad = $turnoSede->getCantidadTurnos();
                        }
                    }else{
                        $cantidad = $turnoSede->getCantidadTurnos();
                    }
                }
            }else{
                $cantidad = $turnoSede->getCantidadTurnos();
            }
            $cantidadPorTurno = $cantidadPorTurno + ( $cantidad * $difMinutos);
        } else {
            $difHoras = $difHoras + ($difMinutos / 60);
            $difHoras = ($difHoras / $turnoSede->getCantidadFrecuencia());
            $cantidad = 0;
            if(count($turnoSede->getTurnoTipoTramite())>0){
                foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno){
                    if($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTramiteId){
                        $cantidad = $tipoTramiteTurno->getCantidadTurno();
                    }else{
                        $cantidad = $turnoSede->getCantidadTurnos();
                    }
                }

            }else{
                $cantidad = $turnoSede->getCantidadTurnos();
            }
            $cantidadPorTurno = $cantidadPorTurno + ($cantidad * $difHoras);
        }
        $cantidadDiaTurnoAux = array();
        if ($turnoSede->getLunes()) {
            if (isset($cantidadDiaTurnoAux[1])) {
                $cantidadDiaTurnoAux[1] = $cantidadDiaTurnoAux[1] + $cantidadPorTurno;
            } else {
                $cantidadDiaTurnoAux[1] = $cantidadPorTurno;
            }
        }
        if ($turnoSede->getMartes()) {
            if (isset($cantidadDiaTurnoAux[2])) {
                $cantidadDiaTurnoAux[2] = $cantidadDiaTurnoAux[2] + $cantidadPorTurno;
            } else {
                $cantidadDiaTurnoAux[2] = $cantidadPorTurno;
            }
        }
        if ($turnoSede->getMiercoles()) {
            if (isset($cantidadDiaTurnoAux[3])) {
                $cantidadDiaTurnoAux[3] = $cantidadDiaTurnoAux[3] + $cantidadPorTurno;
            } else {
                $cantidadDiaTurnoAux[3] = $cantidadPorTurno;
            }
        }
        if ($turnoSede->getJueves()) {
            if (isset($cantidadDiaTurnoAux[4])) {
                $cantidadDiaTurnoAux[4] = $cantidadDiaTurnoAux[4] + $cantidadPorTurno;
            } else {
                $cantidadDiaTurnoAux[4] = $cantidadPorTurno;
            }
        }
        if ($turnoSede->getViernes()) {
            if (isset($cantidadDiaTurnoAux[5])) {
                $cantidadDiaTurnoAux[5] = $cantidadDiaTurnoAux[5] + $cantidadPorTurno;
            } else {
                $cantidadDiaTurnoAux[5] = $cantidadPorTurno;
            }
        }
        if ($turnoSede->getSabado()) {
            if (isset($cantidadDiaTurnoAux[6])) {
                $cantidadDiaTurnoAux[6] = $cantidadDiaTurnoAux[6] + $cantidadPorTurno;
            } else {
                $cantidadDiaTurnoAux[6] = $cantidadPorTurno;
            }
        }

        //Calculo cuantos turnos pueden atender por día
        $iterator = $diaRecorrido;
        while ($iterator <= $ultimoDiaMes) {
            if (isset($cantidadDiaTurnoAux[$this->util->getDiaSemana($iterator, $mes, $anio)])) {
                if($this->perteneceVigencia($turnoSede,$iterator,$ultimoDiaMes,$mes,$anio)) {
                    if (isset($cantidadDiaTurno[$iterator])) {
                        $cantidadDiaTurno[$iterator] = $cantidadDiaTurno[$iterator] + $cantidadDiaTurnoAux[$this->util->getDiaSemana($iterator, $mes, $anio)];
                    } else {
                        $cantidadDiaTurno[$iterator] = $cantidadDiaTurnoAux[$this->util->getDiaSemana($iterator, $mes, $anio)];
                    }
                }
            }
            $iterator++;
        }
        return $cantidadDiaTurno;
    }

    private function perteneceVigencia($turnoSede,$diaRecorrido,$ultimoDiaMes,$mes,$anio){
        $pertenece = true;
        if(is_null($turnoSede->getVigenciaDesdeDateTime())){
            $pertenece = true;
        }else if($turnoSede->getVigenciaDesdeDateTime() <=  $this->util->getFechaDateTime($diaRecorrido . '/' . $mes . '/' . $anio, '23:59:59')){
            $pertenece = true;
        }else{
            $pertenece = false;
        }
        if($pertenece){
            if(is_null($turnoSede->getVigenciaHastaDateTime())){
                $pertenece = true;
            }else if($turnoSede->getVigenciaHastaDateTime() >= $this->util->getFechaDateTime($diaRecorrido . '/' . $mes . '/' . $anio, '23:59:59')){
                $pertenece = true;
            }else{
                $pertenece = false;
            }
        }
        return $pertenece;
    }

    public function getHorasDisponibles($dia,$mes,$anio,$tipoTurnoId,$sedeId){
        $busca =false;
        $horasHabiles = array();
        $diaDesde = $this->util->getFechaDateTime(sprintf("%02d",$dia) . '/' . sprintf("%02d",$mes) . '/' . $anio, '00:00:00');
        $diaHasta = $this->util->getFechaDateTime(sprintf("%02d",$dia) . '/' .sprintf("%02d", $mes) . '/' . $anio, '23:59:59');

        if(intval(date('m'))==$mes) {
            if (intval(date('d')) <= $dia) {
                $busca =true;
            }else{
                $busca =false;
            }
        }else if($mes> intval(date('m'))) {
            $busca =true;
        }else {
            $busca =false;
        }

        if($busca){
            //busco la parametrización por día (teniendo en cuenta las vigencias)
            $repositoryTS = $this->em->getRepository('AdminBundle:TurnoSede')->createQueryBuilder('ts')
                ->where('ts.sede = :sedeId AND ts.activo = true ')->setParameter('sedeId', $sedeId);
            $turnosSede = $repositoryTS->getQuery()->getResult();

            $turnosSedeArray = array();

            $turnosDeldia = array();
            $existe = false;
            foreach ($turnosSede as $turnoSede) {
                $diaActual = false;
                if (intval(date('d')) == $dia) {
                    $diaActual = true;
                }

                $tempHora = ($this->util->getHoraDateTime($turnoSede->getHoraTurnosDesde()->format('H:i: A')));
                $temp2Hora = ($this->util->getHoraDateTime($turnoSede->getHoraTurnosHasta()->format('H:i: A')));
                $tempFrecuencia = $turnoSede->getCantidadFrecuencia();

                $turnoSedeDefineTramite = false;
                if(count($turnoSede->getTurnoTipoTramite())>0){
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno){
                        if($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId){
                            $existeTipoTramiteSede = true;
                            $turnoSedeDefineTramite = true;
                        }
                    }
                    if($turnoSedeDefineTramite){
                        $turnosDeldia = $this->getCantidadHoraTurno($tipoTurnoId,$turnoSede,$turnosDeldia,$dia,$mes,$anio,$diaActual);
                        $turnosSedeUtilizados[] = $turnoSede;
                    }
                }else{
                    $turnosDeldia = $this->getCantidadHoraTurno($tipoTurnoId,$turnoSede,$turnosDeldia,$dia,$mes,$anio,$diaActual);
                    $turnosSedeUtilizados[] = $turnoSede;
                }

                /*$turnosDeldia = $this->getCantidadHoraTurno($tipoTurnoId,$turnoSede,$turnosDeldia,$dia,$mes,$anio,$diaActual);
                if(count($turnoSede->getTurnoTipoTramite())>0){

                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno){
                        if($tipoTramiteTurno->getActivo()) {
                            if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId) {
                                $existe = true;
                                $turnosSedeArray[] = array($tempHora, $tipoTramiteTurno->getCantidadSlot(),$tempFrecuencia ,$temp2Hora);
                            }
                        }
                    }
                }*/
            }

            //Busca los turnos reservados
            $repositoryT = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p')
                ->where('p.sede = :sedeId')->setParameter('sedeId', $sedeId)
                ->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                ->andWhere('p.fechaCancelado IS NULL')
                ->setParameter('fecha_turno_desde', $diaDesde)->setParameter('fecha_turno_hasta', $diaHasta);

            if($existe){
                $repositoryT->andWhere('p.tipoTramite = :tipo_tramite')->setParameter('tipo_tramite', $tipoTurnoId);
            }
            $turnos = $repositoryT->getQuery()->getResult();
            foreach ($turnos as $turno) {
                if (isset($turnosDeldia[$turno->getHoraTurno()->format('H:i')])) {
                    $turnosDeldia[$turno->getHoraTurno()->format('H:i')] = $turnosDeldia[$turno->getHoraTurno()->format('H:i')] - 1;
                }
                //Controlo si el turno pase mas de un slot
                $repositoryTT = $this->em->getRepository('AdminBundle:TurnoTipoTramite')->createQueryBuilder('tt')
                    ->innerJoin('AdminBundle:TurnoSede','ts','WITH','tt.turnosSede = ts.id')
                    ->where('(tt.tipoTramite = :tipoTramite) AND tt.activo = true')->setParameter('tipoTramite', $tipoTurnoId)
                    ->andWhere('(ts.sede = :sedeId) AND ts.activo = true ')->setParameter('sedeId', $sedeId)
                    ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')->setParameter('horaTurno',$this->util->getHoraString($turno->getHoraTurno()))
                ;
                $turnosTramites = $repositoryTT->getQuery()->getResult();
                //Controlo que sea con regla de un tipoTurno o global
                if(count($turnosTramites) > 0 ){ //corresponde a uno que tiene defino
                    foreach ($turnosTramites as $turnoTramite){
                        $slot = $turnoTramite->getCantidadSlot();
                        $intervalo = new \DateInterval('PT' . $turnoTramite->getTurnoSede()->getCantidadFrecuencia() . 'M');
                        $horaDesde = $turno->getHoraTurno();
                        while($slot > 1){
                            $horaDesde->add($intervalo);
                            if (isset($turnosDeldia[$horaDesde->format('H:i')])) {
                                $turnosDeldia[$horaDesde->format('H:i')] = $turnosDeldia[$horaDesde->format('H:i')] - 1;
                            }
                            $slot --;
                        }
                    }
                }else { //como no tiene definido un tiempo por tipo de tramit. Busco todos los turnos que tiene ocupan slots
                    //Controlo si el turno pase mas de un slot
                    $repositoryTT = $this->em->getRepository('AdminBundle:TurnoTipoTramite')->createQueryBuilder('tt')
                        ->innerJoin('AdminBundle:TurnoSede','ts','WITH','tt.turnosSede = ts.id')
                        ->where('tt.activo = true')
                        ->andWhere('(ts.sede = :sedeId) AND ts.activo = true ')->setParameter('sedeId', $sedeId)
                        ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')->setParameter('horaTurno',$this->util->getHoraString($turno->getHoraTurno()))
                    ;
                    $turnosTramites = $repositoryTT->getQuery()->getResult();
                    foreach ($turnosTramites as $turnoTramite){
                        $slot = $turnoTramite->getCantidadSlot();
                        $intervalo = new \DateInterval('PT' . $turnoTramite->getTurnoSede()->getCantidadFrecuencia() . 'M');
                        $horaDesde = $turno->getHoraTurno();
                        while($slot > 1){
                            $horaDesde->add($intervalo);
                            if (isset($turnosDeldia[$horaDesde->format('H:i')])) {
                                $turnosDeldia[$horaDesde->format('H:i')] = $turnosDeldia[$horaDesde->format('H:i')] - 1;
                            }
                            $slot --;
                        }
                    }
                }
            }

            //Controlo si tiene asociado un Tipo Tramite que ocupa varios slots
            $turnosDeldia = $this->controlaDisponibilidadTipoTramiteConSlot($dia,$mes,$anio,$sedeId,$tipoTurnoId,$turnosDeldia,$turnosSedeArray);

            foreach ($turnosDeldia as $clave => $valor) {
                if($valor > 0){
                    $horasHabiles[] = $clave;
                }
            }

            $horasHabiles = array( 'horasHabiles' => $horasHabiles);
        }
        return $horasHabiles;
    }

    private function controlaDisponibilidadTipoTramiteConSlot($dia,$mes,$anio,$sedeId,$tipoTurnoId,$turnosDeldia,$turnosSedes){
        foreach ($turnosSedes as $turnosSederec){
            $intervalo = new \DateInterval('PT' . $turnosSederec[2] . 'M');
            $horaDesde = $turnosSederec[0];
            while( $horaDesde < $turnosSederec[3] ){
                $slot = $turnosSederec[1];
                $horaTemp = ($this->util->getHoraDateTime($horaDesde->format('H:i: A')));;
                $paso = true;
                while($slot > 1){
                    $horaTemp->add($intervalo);;
                    if (isset($turnosDeldia[$horaTemp->format('H:i')])) {
                        if($turnosDeldia[$horaTemp->format('H:i')] < 1 ){
                            $paso = false;
                        }
                    }else{
                        $paso = false;
                    }
                    $slot --;
                }
                if($paso == false){
                    if(isset($turnosDeldia[$horaDesde->format('H:i')])){
                        $turnosDeldia[$horaDesde->format('H:i')] = 0;
                    }
                }
                $horaDesde->add($intervalo);
            }

        }
        return $turnosDeldia;
    }

    private function getCantidadHoraTurno($tipoTurnoId,$turnoSede,$cantidadDiaTurno,$dia,$mes,$anio,$diaActual){
        //Obtengo la cantidad de horas que atienden en la sede
        $cantidadTurnosSegudo = 1;
        $horaDesde = $turnoSede->getHoraTurnosDesde();
        $horaHasta = $turnoSede->getHoraTurnosHasta();
        $horasTurno = $horaHasta->diff($horaDesde);
        $difHoras = intval($horasTurno->format('%H'));
        $difMinutos = intval($horasTurno->format('%i'));

        //Obtengo como esta repartido los turnos
        $turnosHora = array();
        $fechaActual = new \DateTime();
        $fechaActual = new \DateTime('1970-01-01'.' '.$fechaActual->format('H:i').':00');
        if($this->verificaTipoTurnoTipoDia($turnoSede,$dia,$mes,$anio)) {
            if ($turnoSede->getFrecunciaTurnoControl() == 'minutos') {
                $cantidad = 0;
                $sinTurnoTipoTramite = true;
                if (count($turnoSede->getTurnoTipoTramite()) > 0) {
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {
                        if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId) {
                            if(!is_null( $tipoTramiteTurno->getCantidadTurno())){
                                $cantidad = $tipoTramiteTurno->getCantidadTurno();
                                $sinTurnoTipoTramite = false;
                            }
                        }
                    }
                }
                if($sinTurnoTipoTramite){
                    $cantidad = $turnoSede->getCantidadTurnos();
                }

                $difMinutos = $difMinutos + ($difHoras * 60);
                $cantidadTurnos = ($difMinutos / $turnoSede->getCantidadFrecuencia());
                $intervalo = new \DateInterval('PT' . $turnoSede->getCantidadFrecuencia() . 'M');



                if($cantidadTurnos > 0){
                    if($diaActual) {
                        if (($horaDesde > $fechaActual)) {
                            $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                        }
                    }else{
                        $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                    }
                    $cantidadTurnos--;
                }
                while ($cantidadTurnos > 0) {
                    $horaDesde->add($intervalo);
                    if($diaActual) {
                        if (($horaDesde > $fechaActual)) {
                            $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                        }
                    }else{
                        $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                    }
                    $cantidadTurnos--;
                }

            } else {
                $sinTurnoTipoTramite = true;
                $cantidad = 0;
                if (count($turnoSede->getTurnoTipoTramite()) > 0) {
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {
                        if(!is_null( $tipoTramiteTurno->getCantidadTurno())){
                            $cantidad = $tipoTramiteTurno->getCantidadTurno();
                            $sinTurnoTipoTramite = false;
                        }
                    }
                }
                if($sinTurnoTipoTramite){
                    $cantidad = $turnoSede->getCantidadTurnos();
                }
                $difHoras = $difHoras + ($difMinutos / 60);
                $cantidadTurnos = ($difMinutos / $turnoSede->getCantidadFrecuencia());
                $intervalo = new \DateInterval('PT' . $turnoSede->getCantidadFrecuencia() . 'H');
                if($cantidadTurnos > 0){
                    if($diaActual) {
                        if (($horaDesde > $fechaActual)) {
                            $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                        }
                    }else{
                        $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                    }
                    $cantidadTurnos--;
                }
                while ($cantidadTurnos > 0) {
                    $horaDesde->add($intervalo);
                    if($horaDesde>$fechaActual OR $diaActual) {
                        $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                    }
                    $cantidadTurnos--;
                }
            }
        }

        if(count($cantidadDiaTurno)>0) {
            foreach ($turnosHora as $clave => $valor) {
                if(isset($turnosHora[$clave])) {
                    $cantidadDiaTurno[$clave] = $valor + $turnosHora[$clave];
                }else {
                    $cantidadDiaTurno[$clave] = $valor;
                }
            }
        }else{
            $cantidadDiaTurno = $turnosHora;
        }
        return $cantidadDiaTurno;
    }

    private function verificaTipoTurnoTipoDia($turnoSede,$dia,$mes,$anio){
        $control = false;
        if ($turnoSede->getLunes()) {
            if($this->util->getDiaSemana($dia, $mes, $anio) == 1){
                $control = true;
            }
        }
        if ($turnoSede->getMartes()) {
            if($this->util->getDiaSemana($dia, $mes, $anio) == 2){
                $control = true;
            }
        }
        if ($turnoSede->getMiercoles()) {
            if($this->util->getDiaSemana($dia, $mes, $anio) == 3){
                $control = true;
            }
        }
        if ($turnoSede->getJueves()) {
            if($this->util->getDiaSemana($dia, $mes, $anio) == 4){
                $control = true;
            }
        }
        if ($turnoSede->getViernes()) {
            if($this->util->getDiaSemana($dia, $mes, $anio) == 5){
                $control = true;
            }
        }
        if ($turnoSede->getSabado()) {
            if($this->util->getDiaSemana($dia, $mes, $anio) == 6){
                $control = true;
            }
        }
        return $control;
    }

    public function controlaDisponibilidad($fechaTurno,$horaTurno,$tipoTurnoId,$sedeId){
        if(is_string($fechaTurno)){
            $fechaTurno = new \DateTime($fechaTurno);
        }
        if(is_string($horaTurno)){
            $horaTurno = new \DateTime($horaTurno);
        }
        $array = $this->getHorasDisponibles(intval($fechaTurno->format('d')),intval($fechaTurno->format('m')),intval($fechaTurno->format('Y')),$tipoTurnoId,$sedeId);
        $array = $array['horasHabiles'];
        if(in_array ($horaTurno->format('H:i'),$array)){
            return true;
        }else{
            return false;
        }
    }

    public function verificaTurnoSinConfirmar($cuit,$mail=null){
        $repositoryT = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p')
            ->where('p.cuit = :cuit')->setParameter('cuit', $cuit);
        if(!is_null($mail)){
            $repositoryT
                ->andWhere('p.mail1 = :mail')->setParameter('mail', $mail);
        }
        $repositoryT
            ->andWhere('p.fechaCancelado IS NULL AND p.fechaConfirmacion IS NULL');
        $turnos = $repositoryT->getQuery()->getResult();
        if(count($turnos)>0){
            return false;
        }else{
            return true;
        }
    }
}