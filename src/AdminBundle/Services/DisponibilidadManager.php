<?php


namespace AdminBundle\Services;

use AdminBundle\Entity\Turnos;
use Doctrine\ORM\EntityManager;

class DisponibilidadManager
{

    private $em;
    private $util;

    public function __construct(EntityManager $em, UtilManager $util )
    {
        $this->em = $em;
        $this->util = $util;
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
    public function getDiasNoDisponibles($tipoTurnoId,$sedeId,$mes=null,$anio=null){
        if(is_null($mes)){
            $mes = intval(date('m'));
        }
        if(is_null($anio)){
            $anio = intval(date('Y'));
        }
        $cont = 0;
        $array = array();
        while($cont < 7){
            $array = $this->getDiasDisponiblesMes($mes,$anio,$tipoTurnoId,$sedeId,$array);
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

    public function getDiasDisponiblesMes($mes,$anio,$tipoTurnoId,$sedeId,$array){
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
            $repositoryTS = $this->em->getRepository('AdminBundle:TurnosSede')->createQueryBuilder('ts')
                ->where('ts.sede = :sedeId')->setParameter('sedeId', $sedeId);
            $turnosSede = $repositoryTS->getQuery()->getResult();
            $turnosDelMes = array();
            $existeTipoTramiteSede = false;
            foreach ($turnosSede as $turnoSede) {
                $turnosDelMes = $this->getCantidadDiaTurno($tipoTurnoId,$turnoSede,$turnosDelMes,$diaRecorrido,$ultimoDiaMes,$mes,$anio);
                if(count($turnoSede->getTurnoTramite())>0){
                    foreach ($turnoSede->getTurnoTramite() as $tipoTramiteTurno){
                        if($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId){
                            $existeTipoTramiteSede = true;
                        }
                    }
                }
            }

            //Busco y Resto por día los turnos  dados
            $repositoryT = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p')
                ->where('p.sede = :sedeId')->setParameter('sedeId', $sedeId)
                ->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                ->setParameter('fecha_turno_desde', $primerDia)->setParameter('fecha_turno_hasta', $ultimoDia);

            if($existeTipoTramiteSede){
                $repositoryT->andWhere('p.tipoTramite = :tipo_tramite')->setParameter('tipo_tramite', $tipoTurnoId);
            }
            $turnos = $repositoryT->getQuery()->getResult();


            foreach ($turnos as $turno) {
                if (isset($turnosDelMes[$turno->getFechaTurno()->format('d')])) {
                    $turnosDelMes[$turno->getFechaTurno()->format('d')] = $turnosDelMes[$turno->getFechaTurno()->format('d')] - 1;
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
                ->where('f.fecha between :fecha_desde  and :fecha_hasta')
                ->setParameter('fecha_desde', $primerDia)->setParameter('fecha_hasta', $ultimoDia);
            $feriados = $repositoryF->getQuery()->getResult();
            foreach ($feriados as $feriado){
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
        return $array;

    }


    private function getCantidadDiaTurno($tipoTurnoId,$turnoSede,$cantidadDiaTurno,$diaRecorrido,$ultimoDiaMes,$mes,$anio){
        //Obtengo la cantidad de horas que atienden en la sede
        $horaDesde = $turnoSede->getHoraTurnosDesdeSinFormato();
        $horaHasta = $turnoSede->getHoraTurnosHastaSinFormato();
        $horasTurno = $horaHasta->diff($horaDesde);
        $difHoras = intval($horasTurno->format('%H'));
        $difMinutos = intval($horasTurno->format('%i'));

        //Obtengo como esta repartido los turnos
        $cantidadPorTurno = 0;
        if ($turnoSede->getFrecunciaTurnoControl() == 'minutos') {
            $difMinutos = $difMinutos + ($difHoras * 60);
            $difMinutos = ($difMinutos / $turnoSede->getCantidadFrecuencia());
            $cantidad = 0;
            if(count($turnoSede->getTurnoTramite())>0){
                foreach ($turnoSede->getTurnoTramite() as $tipoTramiteTurno){
                    if($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId){
                        $cantidad = $tipoTramiteTurno->getCantidadTurno();
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
            if(count($turnoSede->getTurnoTramite())>0){
                foreach ($turnoSede->getTurnoTramite() as $tipoTramiteTurno){
                    if($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId){
                        $cantidad = $tipoTramiteTurno->getCantidadTurno();
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
            $repositoryTS = $this->em->getRepository('AdminBundle:TurnosSede')->createQueryBuilder('ts')
                ->where('ts.sede = :sedeId')->setParameter('sedeId', $sedeId);
            $turnosSede = $repositoryTS->getQuery()->getResult();

            $turnosDeldia = array();
            $existe = false;
            foreach ($turnosSede as $turnoSede) {
                $diaActual = false;
                if (intval(date('d')) == $dia) {
                    $diaActual = true;
                }
                $turnosDeldia = $this->getCantidadHoraTurno($tipoTurnoId,$turnoSede,$turnosDeldia,$dia,$mes,$anio,$diaActual);

                if(count($turnoSede->getTurnoTramite())>0){
                    foreach ($turnoSede->getTurnoTramite() as $tipoTramiteTurno){
                        if($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId){
                            $existe = true;
                        }
                    }
                }
            }

            //Busca los turnos reservados
            $repositoryT = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p')
                ->where('p.sede = :sedeId')->setParameter('sedeId', $sedeId)
                ->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                ->setParameter('fecha_turno_desde', $diaDesde)->setParameter('fecha_turno_hasta', $diaHasta);


            if($existe){
                $repositoryT->andWhere('p.tipoTramite = :tipo_tramite')->setParameter('tipo_tramite', $tipoTurnoId);
            }

            $turnos = $repositoryT->getQuery()->getResult();
            foreach ($turnos as $turno) {
                if (isset($turnosDeldia[$turno->getHoraTurno()->format('H:i')])) {
                    $turnosDeldia[$turno->getHoraTurno()->format('H:i')] = $turnosDeldia[$turno->getHoraTurno()->format('H:i')] - 1;
                }
            }

            foreach ($turnosDeldia as $clave => $valor) {
                if($valor > 0){
                    $horasHabiles[] = $clave;
                }
            }

            $horasHabiles = array( 'horasHabiles' => $horasHabiles);
        }
        return $horasHabiles;
    }

    private function getCantidadHoraTurno($tipoTurnoId,$turnoSede,$cantidadDiaTurno,$dia,$mes,$anio,$diaActual){
        //Obtengo la cantidad de horas que atienden en la sede
        $horaDesde = $turnoSede->getHoraTurnosDesdeSinFormato();
        $horaHasta = $turnoSede->getHoraTurnosHastaSinFormato();
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
                if (count($turnoSede->getTurnoTramite()) > 0) {
                    foreach ($turnoSede->getTurnoTramite() as $tipoTramiteTurno) {
                        if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId) {
                            $cantidad = $tipoTramiteTurno->getCantidadTurno();
                        }
                    }
                } else {
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
                $cantidad = 0;
                if (count($turnoSede->getTurnoTramite()) > 0) {
                    foreach ($turnoSede->getTurnoTramite() as $tipoTramiteTurno) {
                        if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId) {
                            $cantidad = $tipoTramiteTurno->getCantidadTurno();
                        }
                    }
                } else {
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
        $array = $this->getHorasDisponibles(intval($fechaTurno->format('d')),intval($fechaTurno->format('m')),intval($fechaTurno->format('Y')),$tipoTurnoId,$sedeId);
        $array = $array['horasHabiles'];
        if(in_array ($horaTurno->format('H:i'),$array)){
            return true;
        }else{
            return false;
        }
    }
}