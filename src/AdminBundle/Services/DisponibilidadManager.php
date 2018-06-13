<?php


namespace AdminBundle\Services;

use AdminBundle\Entity\Turnos;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Date;

class DisponibilidadManager
{
    private $em;
    private $util;
    private $mesAtincipacionTurnos;

    public function __construct(EntityManager $em, UtilManager $util)
    {
        $this->em = $em;
        $this->util = $util;
    }

    /**
     * Obtiene el servicio Manager.util
     * @return UtilManager
     */
    public function getUtil(){
        return $this->util;
    }

    /**
     * Setea la cantidad de Meses que se puede sacar los turnos
     * @param $mesAtincipacionTurnos
     */
    public function setMesAtincipacionTurnos($mesAtincipacionTurnos)
    {
        $this->mesAtincipacionTurnos=$mesAtincipacionTurnos;
    }

    /**
     * Retorna la cantidad de meses que se puede sacar los turnos
     * @return $mesAtincipacionTurnos
     */
    public function getMesAtincipacionTurnos()
    {
        return $this->mesAtincipacionTurnos;
    }

    /**
     * obtiene las opciones Generales guardadas, devuelve en array si se al parametro es = true
     * @param bool $hydrate_array
     * @return array|Collection
     */
    public function getOpcionesGenerales($hydrate_array = false)
    {
        $opcionesGenerales= $this->em->getRepository('AdminBundle:OpcionGeneral');
        return $opcionGeneral = $opcionesGenerales->getOpcionesGenerales($hydrate_array);
    }

    /**
     * obtiene las opciones Generales guardadas para la web, devuelve en array si se al parametro es = true
     * @param bool $hydrate_array
     * @return array|Collection
     */
    public function getOpcionesGeneralesWeb($hydrate_array = false)
    {
        $opcionesGenerales= $this->em->getRepository('AdminBundle:OpcionGeneral');
        return $opcionGeneral = $opcionesGenerales->getOpcionesGeneralesConTramiteSoloWeb($hydrate_array);
    }



    /**
     * Se obtiene los tipos de tramite en base a la opcion general pasada, devuelve en array si el parametro $hydrate_array = true
     * @param $opcionGeneralId
     * @param bool $hydrate_array
     * @return array|Collection
     */
    public function obtenerTipoTramite($opcionGeneralId, $hydrate_array=false)
    {
        $tiposTramites= $this->em->getRepository('AdminBundle:TipoTramite');
        return $opcionGeneral = $tiposTramites->getTipoTramiteByOpcionesGenerales($opcionGeneralId, $hydrate_array);
    }

    /**
     * Obtiene las sedes que utilizan un tipo de tramite, devuelve en array si el parametro $hydrate_array = true
     * @param $tipoTramiteId
     * @param bool $hydrate_array
     * @return array|Collection
     */
    public function obtenerSedePorTipoTramte($tipoTramiteId, $hydrate_array = false)
    {
        $tipoTramite = $this->em->getRepository('AdminBundle:TipoTramite')->findOneById($tipoTramiteId);
        $array = array();
        foreach ($tipoTramite->getTurnoTipoTramite() as $turnoTipoTramte) {
            if ($turnoTipoTramte) {
                $turnoSede = $turnoTipoTramte->getTurnoSede();
                if ($turnoSede) {
                    $sede = $turnoSede->getSede();
                    if ($sede) {
                        if ($hydrate_array) {
                            $array[] = array(
                                                                'id'=>$sede->getId(),
                                                                'sede'=>$sede->getSede(),
                                                                'direccion'=>$sede->getDireccion(),
                                                                'letra'=>$sede->getLetra(),
                                                            );
                        } else {
                            $array[] = $sede;
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
     * @param integer $mes
     * @param integer $anio
     * @param integer $tipoTurnoId
     * @param integer $sedeId
     *
     * @return array
     */
    public function getDiasNoDisponibles($tipoTramiteId, $sedeId, $mes=null, $anio=null)
    {
        if (is_null($mes)) {
            $mes = intval(date('m'));
        }
        if (is_null($anio)) {
            $anio = intval(date('Y'));
        }
        $cont = 0;
        $array = array();
        while ($cont < ($this->mesAtincipacionTurnos + 1)) {
            $array = $this->getDiasDisponiblesMes($mes, $anio, $tipoTramiteId, $sedeId, $array);
            if ($mes<12) {
                $mes++;
            } else {
                $mes = 1;
                $anio ++;
            }
            $cont++;
        }
        return $array;
    }

    public function getDiasDisponiblesMes($mes, $anio, $tipoTramiteId, $sedeId, $array)
    {
        $diaRecorrido = 1;
        $diaHabil =array();
        $turnosDelMes = array();
        $busca =false;

        //busca si la fecha actual no sea del pasado
        if(intval(date('Y') == $anio)){
            if (intval(date('m')) == $mes) {
                $busca = true;
            } elseif ($mes > intval(date('m'))) {
                $busca = true;
            } else {
                $busca = false;
            }
        }elseif(intval( $anio > date('Y') )){
            $busca = true;
        }else {
            $busca = false;
        }

        /*
        if (intval(date('m'))==$mes) {
            if (intval(date('d')) >= $diaRecorrido) {
                $diaRecorrido = intval(date('d'));
            }
            $busca =true;
        } elseif ($mes> intval(date('m'))) {
            $busca =true;
        } else {
            $busca =false;
        }
        */

        if ($busca) {
            $primerDia = $this->util->getFechaDateTime(sprintf("%02d", $diaRecorrido) . '/' . sprintf("%02d", $mes) . '/' . $anio, '00:00:00');
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
                if (count($turnoSede->getTurnoTipoTramite())>0) {
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {
                        if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTramiteId) {
                            $existeTipoTramiteSede = true;
                            $turnoSedeDefineTramite = true;
                        }
                    }
                    if ($turnoSedeDefineTramite) {
                        $turnosDelMes = $this->getCantidadDiaTurno($tipoTramiteId, $turnoSede, $turnosDelMes, $diaRecorrido, $ultimoDiaMes, $mes, $anio);
                        $turnosSedeUtilizados[] = $turnoSede;
                    }
                } else {
                    $turnosDelMes = $this->getCantidadDiaTurno($tipoTramiteId, $turnoSede, $turnosDelMes, $diaRecorrido, $ultimoDiaMes, $mes, $anio);
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
                ->setParameter('turnosSedes', $turnosSedeUtilizados);


            if ($existeTipoTramiteSede) {
                $repositoryT->andWhere('p.tipoTramite = :tipo_tramite')->setParameter('tipo_tramite', $tipoTramiteId);
            }
            $turnos = $repositoryT->getQuery()->getResult();


            foreach ($turnos as $turno) {
                if (isset($turnosDelMes[$turno->getFechaTurno()->format('d')])) {
                    //Controlo si el turno pase mas de un slot
                    $repositoryTT = $this->em->getRepository('AdminBundle:TurnoTipoTramite')->createQueryBuilder('tt')
                        ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'tt.turnoSede = ts.id')
                        ->where('(tt.tipoTramite = :tipoTramite) AND tt.activo = true')->setParameter('tipoTramite', $tipoTramiteId)
                        ->andWhere('(ts.sede = :sedeId) AND ts.activo = true ')->setParameter('sedeId', $sedeId)
                        ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')->setParameter('horaTurno', $turno->getHoraTurno());
                    $turnosTramites = $repositoryTT->getQuery()->getResult();

                    $suma = 1;
                    foreach ($turnosTramites as $turnoTramite) {
                        if (!is_null($turnoTramite->getCantidadSlot())) {
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
                } else {
                    $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> $iterator);
                }
                $iterator++;
            }

            //Feriados
            $repositoryF = $this->em->getRepository('AdminBundle:Feriado', 'f')->createQueryBuilder('f')
                ->where('( ( f.fecha between :fecha_desde  and :fecha_hasta ) OR ( f.repiteAnio = true) AND f.activo = true)')
                ->setParameter('fecha_desde', $primerDia)->setParameter('fecha_hasta', $ultimoDia);
            $feriados = $repositoryF->getQuery()->getResult();
            foreach ($feriados as $feriado) {
                if ($feriado->getRepiteAnio()) {
                    if ($feriado->getFecha()->format('m') == $mes) {
                        if (is_null($feriado->getSede())) {
                            if (!in_array($feriado->getFecha()->format('d'), $diaHabil)) {
                                $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($feriado->getFecha()->format('d')));
                            }
                        } else {
                            if ($feriado->getSede()->getId()== $sedeId) {
                                if (!in_array($feriado->getFecha()->format('d'), $diaHabil)) {
                                    $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($feriado->getFecha()->format('d')));
                                }
                            }
                        }
                    }
                } else {
                    if (is_null($feriado->getSede())) {
                        if (!in_array($feriado->getFecha()->format('d'), $diaHabil)) {
                            $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($feriado->getFecha()->format('d')));
                        }
                    } else {
                        if ($feriado->getSede()->getId()== $sedeId) {
                            if (!in_array($feriado->getFecha()->format('d'), $diaHabil)) {
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
            foreach ($cancelaciones as $cancelacion) {
                if ($cancelacion->getSede()->getId()== $sedeId) {
                    if (!in_array($cancelacion->getFecha()->format('d'), $diaHabil)) {
                        $array[] = array('anio' => $anio,'mes'=>$mes,'dia'=> intval($cancelacion->getFecha()->format('d')));
                    }
                }
            }
        }

        return $array;
    }

    private function getCantidadDiaTurno($tipoTramiteId, $turnoSede, $cantidadDiaTurno, $diaRecorrido, $ultimoDiaMes, $mes, $anio)
    {
        //Obtengo las cantidades de Turnos por hora
        $cantidadPorTurno = $this->obtenerCantidadesTurnosPorHorario($turnoSede,$tipoTramiteId);

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
                if ($this->perteneceVigencia($turnoSede, $iterator, $ultimoDiaMes, $mes, $anio)) {
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

    public function obtenerCantidadesTurnosPorHorario($turnoSede,$tipoTramiteId){
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
            if (count($turnoSede->getTurnoTipoTramite())>0) {
                foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {
                    if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTramiteId) {
                        if (!is_null($tipoTramiteTurno->getCantidadTurno()) or $tipoTramiteTurno->getCantidadTurno() > 0) {
                            $cantidad = $tipoTramiteTurno->getCantidadTurno();
                        } else {
                            $cantidad = $turnoSede->getCantidadTurnos();
                        }
                    } else {
                        $cantidad = $turnoSede->getCantidadTurnos();
                    }
                }
            } else {
                $cantidad = $turnoSede->getCantidadTurnos();
            }
            $cantidadPorTurno = $cantidadPorTurno + ($cantidad * $difMinutos);
        } else {
            $difHoras = $difHoras + ($difMinutos / 60);
            $difHoras = ($difHoras / $turnoSede->getCantidadFrecuencia());
            $cantidad = 0;
            if (count($turnoSede->getTurnoTipoTramite())>0) {
                foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {
                    if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTramiteId) {
                        $cantidad = $tipoTramiteTurno->getCantidadTurno();
                    } else {
                        $cantidad = $turnoSede->getCantidadTurnos();
                    }
                }
            } else {
                $cantidad = $turnoSede->getCantidadTurnos();
            }
            $cantidadPorTurno = $cantidadPorTurno + ($cantidad * $difHoras);
        }
        return $cantidadPorTurno;
    }

    private function perteneceVigencia($turnoSede, $diaRecorrido, $ultimoDiaMes, $mes, $anio)
    {
        $pertenece = true;
        if (is_null($turnoSede->getVigenciaDesdeDateTime())) {
            $pertenece = true;
        } elseif ($turnoSede->getVigenciaDesdeDateTime() <=  $this->util->getFechaDateTimeFromVars($diaRecorrido, $mes, $anio, '00:00:00')) {
            $pertenece = true;
        } else {
            $pertenece = false;
        }

        if ($pertenece) {
            if (is_null($turnoSede->getVigenciaHastaDateTime())) {
                $pertenece = true;
            } elseif ($turnoSede->getVigenciaHastaDateTime() >= $this->util->getFechaDateTimeFromVars($diaRecorrido, $mes, $anio, '23:59:59')) {
                $pertenece = true;
            } else {
                $pertenece = false;
            }
        }
        return $pertenece;
    }

    /**
     * Procedmiento que permite obtener los horarios diponible de una sede/tipo de tramite para un dia en particular
     *
     * @param $dia
     * @param $mes
     * @param $anio
     * @param $tipoTurnoId
     * @param $sedeId
     * @param bool $conTurnoSede
     * @param bool $contabilizarSoloSinTurno
     * @return array
     */
    public function getHorasDisponibles($dia, $mes, $anio, $tipoTurnoId, $sedeId, $conTurnoSede = false,$contabilizarSoloSinTurno=false)
    {
        $busca =false;
        $horasHabiles = array();
        $diaDesde = $this->util->getFechaDateTime(sprintf("%02d", $dia) . '/' . sprintf("%02d", $mes) . '/' . $anio, '00:00:00');
        $diaHasta = $this->util->getFechaDateTime(sprintf("%02d", $dia) . '/' .sprintf("%02d", $mes) . '/' . $anio, '23:59:59');

        //busca si la fecha actual no sea del pasado
        if(intval(date('Y') == $anio)){
            if (intval(date('m')) == $mes) {
                if (intval(date('d')) <= $dia) {
                    $busca = true;
                } else {
                    $busca = false;
                }
            } elseif ($mes > intval(date('m'))) {
                $busca = true;
            } else {
                $busca = false;
            }
        }elseif(intval( $anio > date('Y') )){
            $busca = true;
        }else {
            $busca = false;
        }

        if ($busca) {
            //busco la parametrización por día (teniendo en cuenta las vigencias)
            $repositoryTS = $this->em->getRepository('AdminBundle:TurnoSede')->createQueryBuilder('ts')
                ->where('ts.sede = :sedeId AND ts.activo = true ')->setParameter('sedeId', $sedeId);
            $turnosSede = $repositoryTS->getQuery()->getResult();

            $turnosSedeArray = array();

            $turnosDeldia = array();
            $turnosSedeUtilizados  = array();
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
                if (count($turnoSede->getTurnoTipoTramite())>0) {
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {
                        if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId) {
                            $existeTipoTramiteSede = true;
                            $turnoSedeDefineTramite = true;
                        }
                    }
                    if ($turnoSedeDefineTramite) {
                        $turnosDeldia = $this->getCantidadHoraTurno($tipoTurnoId, $turnoSede, $turnosDeldia, $dia, $mes, $anio, $diaActual,false,$contabilizarSoloSinTurno);

                        if ($conTurnoSede) {
                            $turnosSedeUtilizados[] = array('turnoSede' => $turnoSede, 'tipoTramite'=> $tipoTurnoId, 'turnosDeldia' => $turnosDeldia);
                        } else {
                            $turnosSedeUtilizados[] = $turnoSede;
                        }
                    }
                } else {
                    $turnosDeldia = $this->getCantidadHoraTurno($tipoTurnoId, $turnoSede, $turnosDeldia, $dia, $mes, $anio, $diaActual,false,$contabilizarSoloSinTurno);

                    if ($conTurnoSede) {
                        $turnosSedeUtilizados[] = array('turnoSede' => $turnoSede,  'tipoTramite'=> false, 'turnosDeldia' => $turnosDeldia);
                    } else {
                        $turnosSedeUtilizados[] = $turnosDeldia;
                    }
                }
            }

            //Busca los turnos reservados que no sean sacados en la recepcion
            $repositoryT = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p')
                ->where('p.sede = :sedeId')->setParameter('sedeId', $sedeId)
                ->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                ->andWhere('p.fechaCancelado IS NULL')
                ->setParameter('fecha_turno_desde', $diaDesde)->setParameter('fecha_turno_hasta', $diaHasta);

            //Para determinar que turnos entregados (turnos entregados sin turnos previo) debo contabilizar
            if($contabilizarSoloSinTurno == true){
                $repositoryT->andWhere('p.viaMostrador = true');
            }else{
                $repositoryT->andWhere('p.viaMostrador = false');
            }

            //Si corresponde a un tipo de tramite  seleccionando
            if ($existe) {
                $repositoryT->andWhere('p.tipoTramite = :tipo_tramite')->setParameter('tipo_tramite', $tipoTurnoId);
            }
            //Obtengo los turnos para contabilizar
            $turnos = $repositoryT->getQuery()->getResult();
            foreach ($turnos as $turno) {

                if (isset($turnosDeldia[$turno->getHoraTurno()->format('H:i')])) {
                    $turnosDeldia[$turno->getHoraTurno()->format('H:i')] = $turnosDeldia[$turno->getHoraTurno()->format('H:i')] - 1;
                }

                //Controlo si el turno pase mas de un slot
                $repositoryTT = $this->em->getRepository('AdminBundle:TurnoTipoTramite')->createQueryBuilder('tt')
                    ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'tt.turnoSede = ts.id')
                    ->where('(tt.tipoTramite = :tipoTramite) AND tt.activo = true')->setParameter('tipoTramite', $tipoTurnoId)
                    ->andWhere('(ts.sede = :sedeId) AND ts.activo = true ')->setParameter('sedeId', $sedeId)
                    ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')->setParameter('horaTurno', $this->util->getHoraString($turno->getHoraTurno()))
                ;
                $turnosTramites = $repositoryTT->getQuery()->getResult();

                //Controlo que sea con regla de un tipoTurno o global
                if (count($turnosTramites) > 0) { //corresponde a uno que tiene defino
                    foreach ($turnosTramites as $turnoTramite) {
                        $slot = $turnoTramite->getCantidadSlot();
                        $intervalo = new \DateInterval('PT' . $turnoTramite->getTurnoSede()->getCantidadFrecuencia() . 'M');
                        $horaDesde = $turno->getHoraTurno();
                        while ($slot > 1) {
                            $horaDesde->add($intervalo);
                            if (isset($turnosDeldia[$horaDesde->format('H:i')])) {
                                $turnosDeldia[$horaDesde->format('H:i')] = $turnosDeldia[$horaDesde->format('H:i')] - 1;
                            }
                            $slot --;
                        }
                    }
                } else { //como no tiene definido un tiempo por tipo de tramite. Busco todos los turnos que tiene ocupan slots
                    //Controlo si el turno pase mas de un slot
                    $repositoryTT = $this->em->getRepository('AdminBundle:TurnoTipoTramite')->createQueryBuilder('tt')
                        ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'tt.turnoSede = ts.id')
                        ->where('tt.activo = true')
                        ->andWhere('(ts.sede = :sedeId) AND ts.activo = true ')->setParameter('sedeId', $sedeId)
                        ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')->setParameter('horaTurno', $this->util->getHoraString($turno->getHoraTurno()))
                    ;
                    $turnosTramites = $repositoryTT->getQuery()->getResult();

                    foreach ($turnosTramites as $turnoTramite) {
                        $slot = $turnoTramite->getCantidadSlot();
                        $intervalo = new \DateInterval('PT' . $turnoTramite->getTurnoSede()->getCantidadFrecuencia() . 'M');
                        $horaDesde = $turno->getHoraTurno();
                        while ($slot > 1) {
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
            $turnosDeldia = $this->controlaDisponibilidadTipoTramiteConSlot($dia, $mes, $anio, $sedeId, $tipoTurnoId, $turnosDeldia, $turnosSedeArray);


            foreach ($turnosDeldia as $clave => $valor) {
                if ($valor > 0) {
                    $horasHabiles[] = $clave;
                }
            }
            if ($conTurnoSede) {
                $horasHabiles = array( 'horasHabiles' => $horasHabiles, 'turnosSedeUtilizados'=>$turnosSedeUtilizados);
            } else {
                $horasHabiles = array( 'horasHabiles' => $horasHabiles);
            }
        }else{
            $horasHabiles = array( 'horasHabiles' => []);
        }

        return $horasHabiles;
    }

    private function controlaDisponibilidadTipoTramiteConSlot($dia, $mes, $anio, $sedeId, $tipoTurnoId, $turnosDeldia, $turnosSedes)
    {
        foreach ($turnosSedes as $turnosSederec) {
            $intervalo = new \DateInterval('PT' . $turnosSederec[2] . 'M');
            $horaDesde = $turnosSederec[0];
            while ($horaDesde < $turnosSederec[3]) {
                $slot = $turnosSederec[1];
                $horaTemp = ($this->util->getHoraDateTime($horaDesde->format('H:i: A')));
                ;
                $paso = true;
                while ($slot > 1) {
                    $horaTemp->add($intervalo);
                    ;
                    if (isset($turnosDeldia[$horaTemp->format('H:i')])) {
                        if ($turnosDeldia[$horaTemp->format('H:i')] < 1) {
                            $paso = false;
                        }
                    } else {
                        $paso = false;
                    }
                    $slot --;
                }
                if ($paso == false) {
                    if (isset($turnosDeldia[$horaDesde->format('H:i')])) {
                        $turnosDeldia[$horaDesde->format('H:i')] = 0;
                    }
                }
                $horaDesde->add($intervalo);
            }
        }
        return $turnosDeldia;
    }

    /**
     * Procedimiento que permite obtener todos los horarios de un turnoSede
     *
     * @param $tipoTurnoId
     * @param $turnoSede
     * @param $cantidadDiaTurno
     * @param $dia
     * @param $mes
     * @param $anio
     * @param $diaActual
     * @param bool $conSinTurno
     * @param bool $contabilizarSoloSinTurno
     *
     * @return array
     */
    public function getCantidadHoraTurno($tipoTurnoId, $turnoSede, $cantidadDiaTurno, $dia, $mes, $anio, $diaActual,$conSinTurno = false,$contabilizarSoloSinTurno = false,$informarTodosLosTurnos = false)
    {
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
        $fechaActual = new \DateTime('1970-01-01' . ' ' . $fechaActual->format('H:i') . ':00');
        //verifico si debo obtener todos la lista.
        if($informarTodosLosTurnos) {
            //busco el horario  completo
            $fechaActual = new \DateTime('1970-01-01' . ' ' . $turnoSede->getHoraTurnosDesde()->format('H:i') . ':00');
        }


        if ($this->verificaTipoTurnoTipoDia($turnoSede, $dia, $mes, $anio)) {
            //Verifico si la frecuencia del turno es en minuto
            if ($turnoSede->getFrecunciaTurnoControl() == 'minutos') {
                $cantidad = 0;
                $sinTurnoTipoTramite = true;

                //Busco si el turnoSede tiene asignado el Tipo de Tramite
                if (count($turnoSede->getTurnoTipoTramite()) > 0) {
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {
                        //Si el Tipo de Turno es igual al que busco, obtenga la cantidad de turno que atiende
                        if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId) {
                            if (!is_null($tipoTramiteTurno->getCantidadTurno())) {
                                $cantidad = $tipoTramiteTurno->getCantidadTurno();
                                $sinTurnoTipoTramite = false;
                            }
                        }
                    }
                }

                //Se determina como se cuenta la cantidad de turnos que se atiende
                //Primero verifico si encuentro el turnoSede
                if ($sinTurnoTipoTramite) {
                    //Verifico como quiero contar, si solo por sin Turno
                    if($contabilizarSoloSinTurno == true){
                        if(!is_null($turnoSede->getCantidadSinTurnos())){
                            $cantidad = $turnoSede->getCantidadSinTurnos();
                        }else{
                            $cantidad = 0;
                        }

                    }else {
                        //Se necesita contar cuantos turnos se antienden en las dos opciones
                        if ($conSinTurno == true AND !is_null($turnoSede->getCantidadSinTurnos())) {
                            //Se suma los das cantidades
                            $cantidad = $turnoSede->getCantidadTurnos() + $turnoSede->getCantidadSinTurnos();
                        } else {
                            //suma solo la cantidad con turnos
                            $cantidad = $turnoSede->getCantidadTurnos();
                        }
                    }
                }

                //obtenidos la cantidad de turnos que se atiende, se pasa a calcular las frecuencia en la cual se atiene
                $difMinutos = $difMinutos + ($difHoras * 60);
                $cantidadTurnos = ($difMinutos / $turnoSede->getCantidadFrecuencia());
                $intervalo = new \DateInterval('PT' . $turnoSede->getCantidadFrecuencia() . 'M');

                //Agrego el primer slot del dia
                if ($cantidadTurnos > 0) {
                    if ($diaActual) {
                        //si debo informar todos los dias, no hago un control, simplemente lo agrego
                        if($informarTodosLosTurnos){
                            $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                        }else {
                            //controlo que la fecha no se haya vencido
                            if (($horaDesde > $fechaActual)) {
                                $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                            }
                        }
                    } else {
                        $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                    }
                    $cantidadTurnos--;
                }

                //agrego los slot en base a la frecuencia determinada
                while ($cantidadTurnos > 0) {
                    $horaDesde->add($intervalo);
                    if ($diaActual) {
                        if (($horaDesde > $fechaActual)) {
                            $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                        }
                    } else {
                        $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                    }
                    $cantidadTurnos--;
                }
            } else {
                //La frecuencia del turnoSede esta en horas
                $sinTurnoTipoTramite = true;
                $cantidad = 0;
                if (count($turnoSede->getTurnoTipoTramite()) > 0) {
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {

                        if (!is_null($tipoTramiteTurno->getCantidadTurno())) {
                            $cantidad = $tipoTramiteTurno->getCantidadTurno();
                            $sinTurnoTipoTramite = false;
                        }
                    }
                }
                if ($sinTurnoTipoTramite) {
                    if($contabilizarSoloSinTurno == true){
                        if(!is_null($turnoSede->getCantidadSinTurnos())){
                            $cantidad = $turnoSede->getCantidadSinTurnos();
                        }else{
                            $cantidad = 0;
                        }

                    }else {
                        if ($conSinTurno == true AND !is_null($turnoSede->getCantidadSinTurnos())) {
                            $cantidad = $turnoSede->getCantidadTurnos() + $turnoSede->getCantidadSinTurnos();
                        } else {
                            $cantidad = $turnoSede->getCantidadTurnos();
                        }
                    }
                }
                $difHoras = $difHoras + ($difMinutos / 60);
                $cantidadTurnos = ($difHoras / $turnoSede->getCantidadFrecuencia());
                if( (floor($cantidadTurnos) > 01) AND (floor($cantidadTurnos) < 49) ) {
                    $cantidadTurnos = round($cantidadTurnos) + 1;
                }else{
                    $cantidadTurnos = round($cantidadTurnos);
                }
                $intervalo = new \DateInterval('PT' . $turnoSede->getCantidadFrecuencia() . 'H');
                if ($cantidadTurnos > 0) {
                    if ($diaActual) {
                        //si debo informar todos los dias, no hago un control, simplemente lo agrego
                        if($informarTodosLosTurnos){
                            $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                        }else {
                            //controlo que la fecha no se haya vencido
                            if($turnoSede->getsoloPresencial()){
                                //Si es solo presencial, determino si el turno que estoy sacando esta dentro del intervalo de atencion
                                $horaHastaIntervalo = new \DateTime('1970-01-01' . ' ' . ($horaDesde->format('H') + $intervalo->h) . ':' . $horaDesde->format('i') . ':00');
                                if (($horaDesde > $fechaActual) or ($fechaActual < $horaHastaIntervalo)) {
                                    $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                                }
                            }else{
                                if (($horaDesde > $fechaActual) ) {
                                    $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                                }
                            }
                        }
                    } else {
                        $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                    }
                    $cantidadTurnos--;
                }
                while ($cantidadTurnos > 0) {
                    $horaDesde->add($intervalo);
                    if ($diaActual) {
                        if (($horaDesde > $fechaActual)) {
                            $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                        }
                    } else {
                        $turnosHora[$horaDesde->format('H:i')] = $cantidad;
                    }
                    $cantidadTurnos--;
                }
            }
        }

        //Actualizo el array$cantidadDiaTurno con los nuevos datos del turnoSede
        if (count($cantidadDiaTurno)>0) {
            foreach ($turnosHora as $clave => $valor) {
                //Controlo que si en el array existe la hora para sumar turnos o creo un nuevo registro
                if (isset($cantidadDiaTurno[$clave])) {
                    $cantidadDiaTurno[$clave] = $valor + $turnosHora[$clave];
                } else {
                    $cantidadDiaTurno[$clave] = $valor;
                }
            }

            //Tambien lo ordeno
            ksort($cantidadDiaTurno);

        } else {
            //Si el array esta vacio lo completo con el array obtenido con los turnosSede
            $cantidadDiaTurno = $turnosHora;
        }

        return $cantidadDiaTurno;
    }

    private function verificaTipoTurnoTipoDia($turnoSede, $dia, $mes, $anio)
    {

        //$control = false;

        //Para debugar
        $control = true;

        if ($turnoSede->getLunes()) {
            if ($this->util->getDiaSemana($dia, $mes, $anio) == 1) {
                $control = true;
            }
        }
        if ($turnoSede->getMartes()) {
            if ($this->util->getDiaSemana($dia, $mes, $anio) == 2) {
                $control = true;
            }
        }
        if ($turnoSede->getMiercoles()) {
            if ($this->util->getDiaSemana($dia, $mes, $anio) == 3) {
                $control = true;
            }
        }
        if ($turnoSede->getJueves()) {
            if ($this->util->getDiaSemana($dia, $mes, $anio) == 4) {
                $control = true;
            }
        }
        if ($turnoSede->getViernes()) {
            if ($this->util->getDiaSemana($dia, $mes, $anio) == 5) {
                $control = true;
            }
        }
        if ($turnoSede->getSabado()) {
            if ($this->util->getDiaSemana($dia, $mes, $anio) == 6) {
                $control = true;
            }
        }
        return $control;
    }

    public function controlaDisponibilidad($fechaTurno, $horaTurno, $tipoTurnoId, $sedeId)
    {
        if (is_string($fechaTurno)) {
            $fechaTurno = new \DateTime($fechaTurno);
        }
        if (is_string($horaTurno)) {
            $horaTurno = new \DateTime($horaTurno);
        }

        //Obtengo los horarios disponibles para el tipo de tramite
        $array = $this->getHorasDisponibles(intval($fechaTurno->format('d')), intval($fechaTurno->format('m')), intval($fechaTurno->format('Y')), $tipoTurnoId, $sedeId, true);
        $arrayHoras = $array['horasHabiles'];
        $arrayTurno = $array['turnosSedeUtilizados'];

        //indice para recorrer el array
        $index = 0;
        //Indice utilizado para determinar si un turnoSede no tiene asociado un tramite y tiene disponibilidad.
        $indiceSede = null;
        while ($index < count($arrayTurno)) {
            //Controlo que el exista disponiblidad del turno para el turnoSede
            if (isset( $arrayTurno[$index]['turnosDeldia'][$horaTurno->format('H:i')])) {
                //determino si existe asociado el tramite al turnoSde
                if ($arrayTurno[$index]['tipoTramite'] != false) {
                    return array('status'=> true,'data'=>$arrayTurno[$index]['turnoSede']);
                }else{
                    //Determina que existe un turnoSede sin tipo de tramite asociado
                    $indiceSede = $index;
                }
            }
            $index++;
        }
        //verfico si encontre un turnoSede sin tipo de tramite
        if(!is_null($indiceSede)){
            return array('status'=> true,'data'=>$arrayTurno[$indiceSede]['turnoSede']);
        }else{
            //No encontre ninguno
            return array('status'=> false);
        }


    }

    public function verificaTurnoSinConfirmarByPersona($cuit, $mail=null)
    {
        $repositoryT = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p')
            ->where('p.cuit = :cuit')->setParameter('cuit', $cuit);
        if (!is_null($mail)) {
            $repositoryT
                ->andWhere('p.mail1 = :mail')->setParameter('mail', $mail);
        }
        $fechaActual = new \DateTime();

        $repositoryT
            ->andWhere('p.fechaCancelado IS NULL 
                            AND p.fechaConfirmacion IS NULL 
                            AND p.turnoSede IS NOT NULL 
                            AND p.fechaTurno > :fecha_actual')

            ->setParameter('fecha_actual', $fechaActual );

        $turnos = $repositoryT->getQuery()->getResult();

        if (count($turnos)>0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Procedmiento que permite obtener los horarios diponible de una sede/tipo de tramite para un dia en particular de forma Presencial
     *
     * @param $dia
     * @param $mes
     * @param $anio
     * @param $tipoTurnoId
     * @param $sedeId
     * @param bool $conTurnoSede
     * @param bool $contabilizarSoloSinTurno
     * @return array
     */
    public function getHorasDisponiblesPresencial($dia, $mes, $anio, $tipoTurnoId, $sedeId, $conTurnoSede = false,$contabilizarSoloSinTurno=false)
    {
        $busca =false;
        $horasHabiles = array();
        $diaDesde = $this->util->getFechaDateTime(sprintf("%02d", $dia) . '/' . sprintf("%02d", $mes) . '/' . $anio, '00:00:00');
        $diaHasta = $this->util->getFechaDateTime(sprintf("%02d", $dia) . '/' .sprintf("%02d", $mes) . '/' . $anio, '23:59:59');

        //busca si la fecha actual no sea del pasado
        if(intval(date('Y') == $anio)){
            if (intval(date('m')) == $mes) {
                if (intval(date('d')) <= $dia) {
                    $busca = true;
                } else {
                    $busca = false;
                }
            } elseif ($mes > intval(date('m'))) {
                $busca = true;
            } else {
                $busca = false;
            }
        }elseif(intval( $anio > date('Y') )){
            $busca = true;
        }else {
            $busca = false;
        }

        if ($busca) {

            //busco la parametrización las Agendas del día (teniendo en cuenta las vigencias)
            $repositoryTS = $this->em
                ->getRepository('AdminBundle:TurnoSede')
                ->createQueryBuilder('ts')
                ->where('ts.sede = :sedeId AND ts.activo = true ')
                ->setParameter('sedeId', $sedeId);

            $turnosSedes = $repositoryTS->getQuery()->getResult();

            $turnosSedeArray = array();
            $turnosDeldia = array();
            $turnosSedeUtilizados  = array();
            $existe = false;

            //recorro todos las agendas cargadas
            foreach ($turnosSedes as $turnoSede) {

                $diaActual = false;
                if (intval(date('d')) == $dia) {
                    $diaActual = true;
                }

                //$tempHora = ($this->util->getHoraDateTime($turnoSede->getHoraTurnosDesde()->format('H:i: A')));
                //$temp2Hora = ($this->util->getHoraDateTime($turnoSede->getHoraTurnosHasta()->format('H:i: A')));
                //$tempFrecuencia = $turnoSede->getCantidadFrecuencia();

                $turnoSedeDefineTramite = false;
                //controla si la Agenda tiene Tipos de Tramites asignados
                if (count($turnoSede->getTurnoTipoTramite())>0) {
                    foreach ($turnoSede->getTurnoTipoTramite() as $tipoTramiteTurno) {
                        if ($tipoTramiteTurno->getTipoTramite()->getId() == $tipoTurnoId) {
                            $existeTipoTramiteSede = true;
                            $turnoSedeDefineTramite = true;
                        }
                    }
                    if ($turnoSedeDefineTramite) {
                        $turnosDeldia = $this->getCantidadHoraTurno($tipoTurnoId, $turnoSede, $turnosDeldia, $dia, $mes, $anio, $diaActual,false,$contabilizarSoloSinTurno);

                        if ($conTurnoSede) {
                            $turnosSedeUtilizados[] = array('turnoSede' => $turnoSede, 'tipoTramite'=> $tipoTurnoId, 'turnosDeldia' => $turnosDeldia);
                        } else {
                            $turnosSedeUtilizados[] = $turnoSede;
                        }
                    }
                } else {
                    $turnosDeldia = $this->getCantidadHoraTurno($tipoTurnoId, $turnoSede, $turnosDeldia, $dia, $mes, $anio, $diaActual,false,$contabilizarSoloSinTurno);

                    if ($conTurnoSede) {
                        $turnosSedeUtilizados[] = array('turnoSede' => $turnoSede,  'tipoTramite'=> false, 'turnosDeldia' => $turnosDeldia);
                    } else {
                        $turnosSedeUtilizados[] = $turnosDeldia;
                    }
                }
            }

            //Busca los turnos reservados que no sean sacados en la recepcion
            $repositoryT = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p')
                ->where('p.sede = :sedeId')
                ->setParameter('sedeId', $sedeId)
                ->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                ->andWhere('p.fechaCancelado IS NULL')
                ->setParameter('fecha_turno_desde', $diaDesde)
                ->setParameter('fecha_turno_hasta', $diaHasta);

            //Para determinar que turnos entregados (turnos entregados sin turnos previo) debo contabilizar
            if($contabilizarSoloSinTurno == true){
                $repositoryT->andWhere('p.viaMostrador = true');
            }else{
                $repositoryT->andWhere('p.viaMostrador = false');
            }

            //Si corresponde a un tipo de tramite  seleccionando
            if ($existe) {
                $repositoryT->andWhere('p.tipoTramite = :tipo_tramite')->setParameter('tipo_tramite', $tipoTurnoId);
            }
            //Obtengo los turnos para contabilizar
            $turnos = $repositoryT->getQuery()->getResult();
            foreach ($turnos as $turno) {

                if (isset($turnosDeldia[$turno->getHoraTurno()->format('H:i')])) {
                    $turnosDeldia[$turno->getHoraTurno()->format('H:i')] = $turnosDeldia[$turno->getHoraTurno()->format('H:i')] - 1;
                }

                //Controlo si el turno pase mas de un slot
                $repositoryTT = $this->em->getRepository('AdminBundle:TurnoTipoTramite')->createQueryBuilder('tt')
                    ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'tt.turnoSede = ts.id')
                    ->where('(tt.tipoTramite = :tipoTramite) AND tt.activo = true')
                    ->setParameter('tipoTramite', $tipoTurnoId)
                    ->andWhere('(ts.sede = :sedeId) AND ts.activo = true ')
                    ->setParameter('sedeId', $sedeId)
                    ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')
                    ->setParameter('horaTurno', $this->util->getHoraString($turno->getHoraTurno()))
                ;
                $turnosTramites = $repositoryTT->getQuery()->getResult();

                //Controlo que sea con regla de un tipoTurno o global
                if (count($turnosTramites) > 0) { //corresponde a uno que tiene defino
                    foreach ($turnosTramites as $turnoTramite) {
                        $slot = $turnoTramite->getCantidadSlot();
                        $intervalo = new \DateInterval('PT' . $turnoTramite->getTurnoSede()->getCantidadFrecuencia() . 'M');
                        $horaDesde = $turno->getHoraTurno();
                        while ($slot > 1) {
                            $horaDesde->add($intervalo);
                            if (isset($turnosDeldia[$horaDesde->format('H:i')])) {
                                $turnosDeldia[$horaDesde->format('H:i')] = $turnosDeldia[$horaDesde->format('H:i')] - 1;
                            }
                            $slot --;
                        }
                    }
                } else { //como no tiene definido un tiempo por tipo de tramite. Busco todos los turnos que tiene ocupan slots
                    //Controlo si el turno pase mas de un slot
                    $repositoryTT = $this->em->getRepository('AdminBundle:TurnoTipoTramite')->createQueryBuilder('tt')
                        ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'tt.turnoSede = ts.id')
                        ->where('tt.activo = true')
                        ->andWhere('(ts.sede = :sedeId) AND ts.activo = true ')->setParameter('sedeId', $sedeId)
                        ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')->setParameter('horaTurno', $this->util->getHoraString($turno->getHoraTurno()))
                    ;
                    $turnosTramites = $repositoryTT->getQuery()->getResult();

                    foreach ($turnosTramites as $turnoTramite) {
                        $slot = $turnoTramite->getCantidadSlot();
                        $intervalo = new \DateInterval('PT' . $turnoTramite->getTurnoSede()->getCantidadFrecuencia() . 'M');
                        $horaDesde = $turno->getHoraTurno();
                        while ($slot > 1) {
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
            $turnosDeldia = $this->controlaDisponibilidadTipoTramiteConSlot($dia, $mes, $anio, $sedeId, $tipoTurnoId, $turnosDeldia, $turnosSedeArray);


            foreach ($turnosDeldia as $clave => $valor) {
                if ($valor > 0) {
                    $horasHabiles[] = $clave;
                }
            }
            if ($conTurnoSede) {
                $horasHabiles = array( 'horasHabiles' => $horasHabiles, 'turnosSedeUtilizados'=>$turnosSedeUtilizados);
            } else {
                $horasHabiles = array( 'horasHabiles' => $horasHabiles);
            }
        }else{
            $horasHabiles = array( 'horasHabiles' => []);
        }

        return $horasHabiles;
    }


}
