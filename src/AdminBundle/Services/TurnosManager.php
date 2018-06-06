<?php


namespace AdminBundle\Services;

use AdminBundle\Entity\ColaTurno;
use AdminBundle\Entity\Comprobante;
use AdminBundle\Entity\Mail;
use AdminBundle\Entity\Turnos;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TurnosManager
{
    private $em;
    private $disponibilidad;
    private $mailer;
    private $secret;
    private $router;

    private $emailFrom = 'turnos.abc@gmail.com';

    public function __construct(EntityManager $em, DisponibilidadManager $disponibilidad)
    {
        $this->em = $em;
        $this->disponibilidad = $disponibilidad;
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    public function setMailer(\Swift_Mailer  $mailer)
    {
        $this->mailer= $mailer;
    }

    public function setRouter(Router $router){
        $this->router = $router;
    }

    /**
     * Obtener turnos sin confirmador
     *
     * @param integer $sedeId
     * @param date $fecha
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function obtenerTodosSinConfimar($sedeId, $fecha)
    {
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha, 3, 2), substr($fecha, 0, 2), substr($fecha, 6, 4)));
        $repository = $this->em->getRepository('AdminBundle:Turno', 'p')->createQueryBuilder('p');
        $repository->where('(p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta) AND p.fechaConfirmacion IS NULL AND p.fechaCancelado IS NULL ')->setParameter('fecha_turno_desde', $fecha.' 00:00:00')->setParameter('fecha_turno_hasta', $fecha.' 23:59:59');
        $repository->andWhere('p.sede = :sedeId')->setParameter('sedeId', $sedeId);
        $repository->orderBy('p.horaTurno', 'ASC');
        return  $repository->getQuery()->getResult();
    }

    /**
     * Obtener turnos via filtro
     *
     * @param integer $sedeId
     * @param time $horaDesde
     * @param time $horaHasta
     * @param integer $estado
     * @param integer $tipoTramite
     * @param date $fecha
     * @param string $cuit
     * @param integer $nroTurno
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function obtenerPorFiltro($sedeId, $horaDesde, $horaHasta, $estado, $tipoTramite, $fecha, $cuit=null, $nroTurno=null)
    {
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha, 3, 2), substr($fecha, 0, 2), substr($fecha, 6, 4)));

        $repository = $this->em->getRepository('AdminBundle:Turno', 'p');
        $repository = $repository->createQueryBuilder('p');

        $hora = (substr($horaDesde, 0, 2));
        $min = (substr($horaDesde, 3, 2));
        if (substr($horaDesde, 6, 2) == 'PM') {
            $hora = $hora +12;
        }
        $hora2 = (substr($horaHasta, 0, 2));
        $min2 = (substr($horaHasta, 3, 2));
        if (substr($horaHasta, 6, 2) == 'PM') {
            $hora2 = $hora2 +12;
        }

        $repository->where('p.horaTurno >= :horaDesde AND p.horaTurno  <=  :horaHasta')
            ->setParameter('horaDesde', ($hora.':'.$min.':00'))
            ->setParameter('horaHasta', ($hora2.':'.$min2).':00');

        $repository->andWhere('p.sede = :sedeId')->setParameter('sedeId', $sedeId);

        $repository->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')->setParameter('fecha_turno_desde', $fecha.' 00:00:00')->setParameter('fecha_turno_hasta', $fecha.' 23:59:59');

        if ($tipoTramite != 0) {
            $repository->andWhere('p.tipoTramite = :tipoTramite')->setParameter('tipoTramite', $tipoTramite);
        }

        if ($cuit) {
            $repository->andWhere('p.cuit = :cuit')->setParameter('cuit', $cuit);
        }

        if ($nroTurno) {
            $repository->andWhere('p.numero = :numero')->setParameter('numero', $nroTurno);
        }

        $sub =  $this->em->createQueryBuilder();
        $sub->select("t");
        $sub->from("AdminBundle:ColaTurno", "t");
        $sub->andWhere('t.turno = p.id AND t.atendido = true');

        switch ($estado) {
            case 0: //Sin Corfirmar
                $repository->andWhere('p.fechaConfirmacion IS NULL AND p.fechaCancelado IS NULL');
                break;
            case 1: //Confirmados
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL');
                break;
            case 2: //Confirmados Sin Turnos
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL');
                break;
            case 3: //Confirmados Con Turnos
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL');
                break;
            case 4: //Atendidos
                $repository->andWhere('p.fechaCancelado IS NULL');
                $repository->andWhere($repository->expr()->exists($sub->getDQL()));
                break;
            case 5: //Atendidos Sin Turnos
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL');
                $repository->andWhere($repository->expr()->exists($sub->getDQL()));
                break;
            case 6: //Atendidos Con Turnos
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL');
                $repository->andWhere($repository->expr()->exists($sub->getDQL()));
                break;
            case 7: //Confirmados y no Atendido

                $sub =  $this->em->createQueryBuilder();
                $sub->select("t");
                $sub->from("AdminBundle:ColaTurno", "t");
                $sub->andWhere('t.turno = p.id AND t.atendido = false');

                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL');
                $repository->andWhere($repository->expr()->exists($sub->getDQL()));
                break;
            case 8: //Cancelados
                $repository->andWhere('p.fechaCancelado IS NOT NULL');
                break;
        }

        $repository->orderBy('p.horaTurno', 'ASC');

        return  $repository->getQuery()->getResult();
    }

    /**
     * Cantidad de Turnos generados
     *
     * @param interger $sedeId
     * @param date $fecha
     *
     * @return integer
     */
    public function getCantidad($sedeId, $fecha)
    {
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha, 3, 2), substr($fecha, 0, 2), substr($fecha, 6, 4)));
        $query =  $this->em->createQuery(
            'SELECT count(t.id) cant
                FROM AdminBundle:Turno t
                WHERE t.sede = :sedeId AND (t.fechaTurno BETWEEN :desde AND :hasta)'
        )->setParameter('desde', $fecha.' 00:00:00')
            ->setParameter('hasta', $fecha.' 23:59:59')
            ->setParameter('sedeId', $sedeId);
        $cantidad = $query->getResult();
        return  $cantidad[0]['cant'];
    }

    /**
     * Cantidad de Turnos confirmados
     *
     * @param interger $sedeId
     * @param date $fecha
     *
     * @return integer
     */
    public function getCantidadConfirmados($sedeId, $fecha)
    {
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha, 3, 2), substr($fecha, 0, 2), substr($fecha, 6, 4)));
        $query =  $this->em->createQuery(
                'SELECT count(t.id) cant
                FROM AdminBundle:Turno t
                WHERE t.sede = :sedeId AND t.fechaConfirmacion IS NOT NULL AND (t.fechaTurno BETWEEN :desde AND :hasta)'
        )->setParameter('desde', $fecha.' 00:00:00')
        ->setParameter('hasta', $fecha.' 23:59:59')
        ->setParameter('sedeId', $sedeId);
        $cantidad = $query->getResult();
        return  $cantidad[0]['cant'];
    }


    /**
     * Cantidad de Turnos creados en mostrador
     *
     * @param interger $sedeId
     * @param date $fecha
     *
     * @return integer
     */
    public function getCantidadSinTurnos($sedeId, $fecha)
    {
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha, 3, 2), substr($fecha, 0, 2), substr($fecha, 6, 4)));
        $query =  $this->em->createQuery(
            'SELECT count(t.id) cant
                FROM AdminBundle:Turno t
                WHERE t.sede = :sedeId AND t.viaMostrador = true AND t.fechaCancelado is null AND (t.fechaTurno BETWEEN :desde AND :hasta)'
        )->setParameter('desde', $fecha.' 00:00:00')
            ->setParameter('hasta', $fecha.' 23:59:59')
            ->setParameter('sedeId', $sedeId);
        $cantidad = $query->getResult();
        return  $cantidad[0]['cant'];
    }

    /**
     * Actualiza Numero Turno Sede
     *
     * @param interger $sedeId
     * @param integer $numeroTurno;
     *
     * @return boolean
     */
    public function actualizaNumeroTurnoSede($sedeId, $numeroTurno)
    {
        try {
            $repository = $this->em->getRepository('AdminBundle:Sede');
            $sede = $repository->findOneById($sedeId);
            if($sede) {
                $sede->setUltimoTurno($numeroTurno);
                $this->em->persist($sede);
                $this->em->flush();
                return true;
            }else{
                throw new \Exception('Error 1.TM.ANTS No se encuentra la sede buscada');
            }
        }catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * Obtener Numero Turno Sede
     *
     * @param interger $sedeId
     *
     * @return integer
     */
    public function obtenerProximoTurnoSede($sedeId)
    {
        try {
            $repository = $this->em->getRepository('AdminBundle:Sede');
            $sede = $repository->findOneById($sedeId);
            if($sede) {
                $proximoNumero = $sede->getUltimoTurno() + 1;
                $sede->setUltimoTurno($proximoNumero);
                $this->em->persist($sede);
                $this->em->flush();
                return $proximoNumero;
            }else{
                throw new \Exception('Error 1.TM.OPTS No se encuentra la sede buscada');
            }
        }catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * Confirma Turno
     *
     * @param AdminBundle:Turno $turno
     * @param UserBundle:User $user
     *
     * @return boolean
     */
    public function confirmarTurno($turno, $user, $prioritario)
    {
        //Commienzo la transaccion
        $this->em->getConnection()->beginTransaction(); // suspend auto-commit
        try {

            $turno->setUsuarioConfirmacion($user);
            $turno->setFechaConfirmacion(new \DateTime("now"));
            $this->em->persist($turno);
            $this->em->flush();

            //Genero el objeto cola
            $cola = new ColaTurno();
            $cola->setSede($turno->getSede());
            $cola->setTurno($turno);
            $cola->setPrioritario($prioritario);
            $cola->setAtendido(false);
            $cola->setActivo(true);
            $cola->setFechaTurno(new \DateTime("now"));

            $cola = $this->obtenerLetraNumeroTurno($turno, $cola, $prioritario);

            $this->em->persist($cola);
            $this->em->flush();
            $this->em->getConnection()->commit();

        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        return true;
    }

    private function obtenerLetraNumeroTurno($turno, $cola, $prioritarios=false)
    {
        $this->em->refresh($turno);

        //Controlo que el turno que se esta por confirmar sea del dia
        $date = new \DateTime('now');
        $diaDesde = $this->disponibilidad->getUtil()->getFechaDateTime($date->format('d/m/Y'), '00:00:00');
        $diaHasta = $this->disponibilidad->getUtil()->getFechaDateTime($date->format('d/m/Y'), '23:59:59');

        if ($diaDesde > $turno->getFechaTurno()) {
            throw new \Exception('Error 4.TM.OLNT No se puee Confirmar el Turno, la fecha del Turno se encuentra vencida');
        }

        $turnoSede = $turno->getTurnoSede();
        $this->em->refresh($turnoSede);

        //array con la cantidad de turnos que se puede asignar a cada turnoSede
        $turnoSedeCantidadTurnos = array();
        //array con la frenciancia de turnos que se puede asignar a cada turnoSede
        $turnoSedeFrecuenciaTurnos = array();


        //determino la letra que corresponde en base a los turnoSede
        $repositoryTS = $this->em->getRepository('AdminBundle:TurnoSede')->createQueryBuilder('ts')
            ->where('ts.sede = :sedeId AND ts.activo = true ')
            ->setParameter('sedeId', $turnoSede->getSede()->getid())
            //->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')
            //->setParameter('horaTurno', $turno->getHoraTurno())
            ->orderBy('ts.id');
        $turnosSede = $repositoryTS->getQuery()->getResult();
        $turnoSedeIndiceLetra =0;
        $indice =0;

        //determino cual turnoSede es para asegnarle la letra
        foreach ($turnosSede as $turnoSedeO) {
            if ($turnoSede->getId() == $turnoSedeO->getId()) {
                $turnoSedeIndiceLetra = $indice;
            }
            //inicilizo la cantidad
            $cantidadTurnosSegudo = 1;
            $horaDesde = $turnoSedeO->getHoraTurnosDesde();
            $horaHasta = $turnoSedeO->getHoraTurnosHasta();
            //Obtengo la clase DateInterval en base a la diferencia desde el inicio y fin de la Agenda
            $horasTurno = $horaHasta->diff($horaDesde);
            $difHoras = intval($horasTurno->format('%H'));
            $difMinutos = intval($horasTurno->format('%i'));
            //cantidad Minutos dentro del horario de antención de la Agenda
            $difMinutos = $difMinutos + ($difHoras * 60);
            //Cantidad de Turnos disponibles por Agenda
            $cantidadTurnos = ($difMinutos / $turnoSedeO->getCantidadFrecuencia());
            //coloco la parte entera de la cantidad de turnos que se atiende por agenda
            $turnoSedeCantidadTurnos[$turnoSedeO->getId()] = intval($cantidadTurnos);
            //coloco la frencia de los turnos por agenda
            $turnoSedeFrecuenciaTurnos[$turnoSedeO->getId()] = $turnoSedeO->getCantidadSinTurnos() + $turnoSedeO->getCantidadTurnos();
            //Controlo si la cantidad de turno es decimal, asi determino si aumento en uno mas los turnos
            $partes = explode(".", $cantidadTurnos);
            if (isset($partes[1])) {
                if ($partes[1] > 0) {
                    $turnoSedeCantidadTurnos[$turnoSedeO->getId()] = $turnoSedeCantidadTurnos[$turnoSedeO->getId()] +1;
                }
            }
            $indice++;
        }

        //determina cuantos turnos se dan por el TurnoSede y horarios
        $tipoTramite = $turno->getTipoTramite()->getId();
        $dia = $turno->getFechaTurno()->format('d');
        $mes = $turno->getFechaTurno()->format('m');
        $anio = $turno->getFechaTurno()->format('Y');
        $turnosDeldia = array();

        $diaActual = false;
        //determino si es el dia actual
        $fechaActual = $fechaActual = new \DateTime();
        if ($dia == $fechaActual->format('d')) {
            if ($mes == $fechaActual->format('m')) {
                if ($anio == $fechaActual->format('Y')) {
                    $diaActual = true;
                }
            }
        }

        //obtendo la distribucion de horario del turnoSede
        $turnosDeldia = $this->disponibilidad->getCantidadHoraTurno($tipoTramite, $turnoSede, array(), $dia, $mes, $anio, $diaActual, true, false, true);

        if (count($turnosDeldia)>0) {
            //en base a la cantidad determino el numero del turno en base la distribucion de turnos que existe.
            $cantidad = 0;
            $numeroTurno = 1;
            foreach ($turnosDeldia as $indice => $turnoDeldia) {
                if (($indice == $turno->getHoraTurno()->format('H:i'))) {
                    //todo determinar si existe ya un turno con este valor (caso que se determine mas de un turno por hora)
                    $fecha = $turno->getFechaTurno()->format('Y/m/d');

                    $sql = "    SELECT p
                                FROM AdminBundle:ColaTurno p
                                INNER JOIN AdminBundle:Turno t WITH p.turno = t.id
                                INNER JOIN AdminBundle:TurnoSede ts WITH t.turnoSede = ts.id
                                WHERE (p.fechaTurno between :fecha_turno_desde and :fecha_turno_hasta)
                                  AND t.horaTurno = :horaTurno
                                  AND p.sede = :sedeId
                                  AND ts.id = :turnoSedeId ";

                    //'AND (:horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta) ";

                    $query = $this->em->createQuery(
                        $sql
                    )->setParameter('fecha_turno_desde', $fecha . ' 00:00:00')
                        ->setParameter('fecha_turno_hasta', $fecha . ' 23:59:59')
                        ->setParameter('horaTurno', $turno->getHoraTurno()->format('H:i:s'))
                        ->setParameter('sedeId', $cola->getSede()->getId())
                        ->setParameter('turnoSedeId', $turnoSede->getId());
                    //->setParameter('horaTurno', $turno->getHoraTurno());

                    $db =  $query->execute();
                    $cantidadCola = count($db);

                    /*
                    $repository = $this->em->getRepository('AdminBundle:ColaTurno', 'p')->createQueryBuilder('p');
                    $repository->innerJoin('AdminBundle:Turno', 't', 'WITH', 'p.turno = t.id');
                    $repository->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 't.turnoSede = ts.id');
                    $repository->where('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                                ->andWhere('t.horaTurno = :horaTurno')
                                ->andWhere('p.sede = :sedeId')
                                ->andWhere('t.tipoTramite = :tipoTramiteId')
                                ->andWhere(' :horaTurno between  ts.horaTurnosDesde and ts.horaTurnosHasta')

                                ->setParameter('fecha_turno_desde', $fecha . ' 00:00:00')
                                ->setParameter('fecha_turno_hasta', $fecha . ' 23:59:59')
                                ->setParameter('horaTurno',$turno->getHoraTurno())
                                ->setParameter('sedeId', $cola->getSede()->getId())
                                ->setParameter('tipoTramiteId', $turno->getTipoTramite()->getId())
                                ->setParameter('horaTurno', $turno->getHoraTurno());

                    $cantidadCola = count($repository->getQuery()->getResult());
                    */

                    //Calculo el proximo numero en base al acumulado que tengo
                    $numeroTurno = $cantidad+$cantidadCola+1;
                    //Sumo el acumulado mas la cantidad de turnos que tengo para la hora
                    $cantidad = $cantidad + ($turnoDeldia);
                } else {
                    //Sumo el acumulado mas la cantidad de turnos que tengo para la hora
                    $cantidad = $cantidad + ($turnoDeldia);
                }
            }

            //controla si los turnoSede  ocupan mas de una letra
            $ultimo = false;
            foreach ($turnoSedeCantidadTurnos as $indicesTurno => $turnoSedeCantidadTurno) {
                if ($indicesTurno == $turnoSede->getId()) {
                    $ultimo = true;
                }
                if ($ultimo == false) {
                    //Controlo si un turnoSede da mas que las dos convinaciones de letras
                    // (Ej: BA entonces la siguiente letra para el turnoSede actual debe ser CA)
                    if (($turnoSedeCantidadTurno*$turnoSedeFrecuenciaTurnos[$indicesTurno]) > 1078) {
                        $saltosLentras = intdiv(($turnoSedeCantidadTurno*$turnoSedeFrecuenciaTurnos[$indicesTurno]), 1078);
                        $turnoSedeIndiceLetra = $turnoSedeIndiceLetra+$saltosLentras;
                    }
                }
            }

            //salto a la siguiente primera letra
            $turnoSedeIndiceLetra = $turnoSedeIndiceLetra *11;

            //Determino la letra a asignarse en base a la cantidad de turnosSede y el numero de Turno
            //Determino el desafase que genera el numero de turno y cuantos saltos de combinaciones de las letras existe
            //Ej: Cantidad 4143, tengo que saltar 42 letras para obtener la del turno
            $saltosLentras = intdiv($numeroTurno, 98);

            //Para determina el nuermo de turno, debe calcular si existen saltos de Letras
            if ($saltosLentras> 0) {
                //calculo el numero numero del turno por vuelve a comenzar la numeracion para la siguiente letra
                //para que tenga el formato AA-11
                $numeroTurno = $numeroTurno % 98;
                //si el numero de turno calculado es 0 le asigno el 1 para comenzar
                if ($numeroTurno == 0) {
                    $numeroTurno =1;
                }
                //Actualizo al Indice de la letra en base al orden de los turnos anterior
                //mas los saltos de letras del turno actual en base al numero de turno que se agenda
                $turnoSedeIndiceLetra = $turnoSedeIndiceLetra +$saltosLentras;
            }

            //Asigno al turno la letra que le corresponde y el numero que luego son mastrados por el Monitor
            $cola->setLetra($this->obtenerLetra($turnoSedeIndiceLetra, $prioritarios));
            $cola->setNumero($numeroTurno);
        } else {
            throw new \Exception('Error 1.TM.OLNT No se ha encontrado Turnos disponibles. Verifique que la Hora del Turno no se encuentre vencido.');
        }
        return $cola;
    }

    public function obtenerLetraTurnoSede($turnoSede,$tipoTramiteId,$prioritarios){

        //Controlo que el turno que se esta por confirmar sea del dia
        $date = new \DateTime('now');
        $diaDesde = $this->disponibilidad->getUtil()->getFechaDateTime($date->format('d/m/Y'), '00:00:00');
        $diaHasta = $this->disponibilidad->getUtil()->getFechaDateTime($date->format('d/m/Y'), '23:59:59');

        $this->em->refresh($turnoSede);

        //array con la cantidad de turnos que se puede asignar a cada turnoSede
        $turnoSedeCantidadTurnos = array();
        //array con la frenciancia de turnos que se puede asignar a cada turnoSede
        $turnoSedeFrecuenciaTurnos = array();


        //determino la letra que corresponde en base a los turnoSede
        $repositoryTS = $this->em->getRepository('AdminBundle:TurnoSede')->createQueryBuilder('ts')
            ->where('ts.sede = :sedeId AND ts.activo = true ')
            ->setParameter('sedeId', $turnoSede->getSede()->getid())
            ->orderBy('ts.id');
        $turnosSede = $repositoryTS->getQuery()->getResult();
        $turnoSedeIndiceLetra =0;
        $indice =0;

        //determino cual turnoSede es para asegnarle la letra
        foreach ($turnosSede as $turnoSedeO) {
            if ($turnoSede->getId() == $turnoSedeO->getId()) {
                $turnoSedeIndiceLetra = $indice;
            }
            //inicilizo la cantidad
            $cantidadTurnosSegudo = 1;
            $horaDesde = $turnoSedeO->getHoraTurnosDesde();
            $horaHasta = $turnoSedeO->getHoraTurnosHasta();
            //Obtengo la clase DateInterval en base a la diferencia desde el inicio y fin de la Agenda
            $horasTurno = $horaHasta->diff($horaDesde);
            $difHoras = intval($horasTurno->format('%H'));
            $difMinutos = intval($horasTurno->format('%i'));
            //cantidad Minutos dentro del horario de antención de la Agenda
            $difMinutos = $difMinutos + ($difHoras * 60);
            //Cantidad de Turnos disponibles por Agenda
            $cantidadTurnos = ($difMinutos / $turnoSedeO->getCantidadFrecuencia());
            //coloco la parte entera de la cantidad de turnos que se atiende por agenda
            $turnoSedeCantidadTurnos[$turnoSedeO->getId()] = intval($cantidadTurnos);
            //coloco la frencia de los turnos por agenda
            $turnoSedeFrecuenciaTurnos[$turnoSedeO->getId()] = $turnoSedeO->getCantidadSinTurnos() + $turnoSedeO->getCantidadTurnos();
            //Controlo si la cantidad de turno es decimal, asi determino si aumento en uno mas los turnos
            $partes = explode(".", $cantidadTurnos);
            if (isset($partes[1])) {
                if ($partes[1] > 0) {
                    $turnoSedeCantidadTurnos[$turnoSedeO->getId()] = $turnoSedeCantidadTurnos[$turnoSedeO->getId()] +1;
                }
            }
            $indice++;
        }

        //determina cuantos turnos se dan por el TurnoSede y horarios
        $dia = $date->format('d');
        $mes = $date->format('m');
        $anio = $date->format('Y');
        $turnosDeldia = array();

        $diaActual = false;
        //determino si es el dia actual
        $fechaActual = $fechaActual = new \DateTime();
        if ($dia == $fechaActual->format('d')) {
            if ($mes == $fechaActual->format('m')) {
                if ($anio == $fechaActual->format('Y')) {
                    $diaActual = true;
                }
            }
        }

        //obtendo la distribucion de horario del turnoSede
        $turnosDeldia = $this->disponibilidad->getCantidadHoraTurno($tipoTramiteId, $turnoSede, array(), $dia, $mes, $anio, $diaActual, true, false, true);


        //en base a la cantidad determino el numero del turno en base la distribucion de turnos que existe.
        $cantidad = 0;
        $numeroTurno = 1;
        foreach ($turnosDeldia as $indice => $turnoDeldia) {

                //Sumo el acumulado mas la cantidad de turnos que tengo para la hora
                $cantidad = $cantidad + ($turnoDeldia);

        }

        //controla si los turnoSede  ocupan mas de una letra
        $ultimo = false;
        foreach ($turnoSedeCantidadTurnos as $indicesTurno => $turnoSedeCantidadTurno) {
            if ($indicesTurno == $turnoSede->getId()) {
                $ultimo = true;
            }
            if ($ultimo == false) {
                //Controlo si un turnoSede da mas que las dos convinaciones de letras
                // (Ej: BA entonces la siguiente letra para el turnoSede actual debe ser CA)
                if (($turnoSedeCantidadTurno*$turnoSedeFrecuenciaTurnos[$indicesTurno]) > 1078) {
                    $saltosLentras = intdiv(($turnoSedeCantidadTurno*$turnoSedeFrecuenciaTurnos[$indicesTurno]), 1078);
                    $turnoSedeIndiceLetra = $turnoSedeIndiceLetra+$saltosLentras;
                }
            }
        }

        //salto a la siguiente primera letra
        $turnoSedeIndiceLetra = $turnoSedeIndiceLetra *11;

        //Determino la letra a asignarse en base a la cantidad de turnosSede y el numero de Turno
        //Determino el desafase que genera el numero de turno y cuantos saltos de combinaciones de las letras existe
        //Ej: Cantidad 4143, tengo que saltar 42 letras para obtener la del turno
        $saltosLentras = intdiv($numeroTurno, 98);

        //Para determina el nuermo de turno, debe calcular si existen saltos de Letras
        if ($saltosLentras> 0) {
            //calculo el numero numero del turno por vuelve a comenzar la numeracion para la siguiente letra
            //para que tenga el formato AA-11
            $numeroTurno = $numeroTurno % 98;
            //si el numero de turno calculado es 0 le asigno el 1 para comenzar
            if ($numeroTurno == 0) {
                $numeroTurno =1;
            }
            //Actualizo al Indice de la letra en base al orden de los turnos anterior
            //mas los saltos de letras del turno actual en base al numero de turno que se agenda
            $turnoSedeIndiceLetra = $turnoSedeIndiceLetra +$saltosLentras;
        }
        $letra = $this->obtenerLetra($turnoSedeIndiceLetra, $prioritarios);
        return $letra;
    }

    /**
     * Busca la letra correspondiente a la cantidad de turnos que se esta dando
     * @param $cantidad
     * @param $reservado
     * @return string
     * @throws \Exception
     */
    private function obtenerLetra($cantidad, $reservado)
    {
        try {
            $letra = '';
            if ($reservado) {
                switch ($cantidad) {
                    /*
                     * Letra M
                     */
                    case 0:
                        $letra = "MA";
                        break;
                    case 1:
                        $letra = "MB";
                        break;
                    case 2:
                        $letra = "MC";
                        break;
                    case 3:
                        $letra = "MD";
                        break;
                    case 4:
                        $letra = "ME";
                        break;
                    case 5:
                        $letra = "MF";
                        break;
                    case 6:
                        $letra = "MG";
                        break;
                    case 7:
                        $letra = "MH";
                        break;
                    case 8:
                        $letra = "MI";
                        break;
                    case 9:
                        $letra = "MJ";
                        break;
                    case 10:
                        $letra = "MK";
                        break;
                    /*
                     * Letra N
                     */
                    case 11:
                        $letra = "NA";
                        break;
                    case 12:
                        $letra = "NB";
                        break;
                    case 13:
                        $letra = "NC";
                        break;
                    case 14:
                        $letra = "ND";
                        break;
                    case 15:
                        $letra = "NE";
                        break;
                    case 16:
                        $letra = "NF";
                        break;
                    case 17:
                        $letra = "NG";
                        break;
                    case 18:
                        $letra = "NH";
                        break;
                    case 19:
                        $letra = "NI";
                        break;
                    case 20:
                        $letra = "NJ";
                        break;
                    case 21:
                        $letra = "NK";
                        break;
                    /*
                     * Letra O
                     */
                    case 22:
                        $letra = "OA";
                        break;
                    case 23:
                        $letra = "OB";
                        break;
                    case 24:
                        $letra = "OC";
                        break;
                    case 25:
                        $letra = "OD";
                        break;
                    case 26:
                        $letra = "OE";
                        break;
                    case 27:
                        $letra = "OF";
                        break;
                    case 28:
                        $letra = "OG";
                        break;
                    case 29:
                        $letra = "OH";
                        break;
                    case 30:
                        $letra = "OI";
                        break;
                    case 31:
                        $letra = "OJ";
                        break;
                    case 32:
                        $letra = "OK";
                        break;
                }
            } else {
                switch ($cantidad) {
                    /*
                     * Letra A
                     */
                    case 0:
                        $letra = "AA";
                        break;
                    case 1:
                        $letra = "AB";
                        break;
                    case 2:
                        $letra = "AC";
                        break;
                    case 3:
                        $letra = "AD";
                        break;
                    case 4:
                        $letra = "AE";
                        break;
                    case 5:
                        $letra = "AF";
                        break;
                    case 6:
                        $letra = "AG";
                        break;
                    case 7:
                        $letra = "AH";
                        break;
                    case 8:
                        $letra = "AI";
                        break;
                    case 9:
                        $letra = "AJ";
                        break;
                    case 10:
                        $letra = "AK";
                        break;
                    /*
                     * Letra B
                     */
                    case 11:
                        $letra = "BA";
                        break;
                    case 12:
                        $letra = "BB";
                        break;
                    case 13:
                        $letra = "BC";
                        break;
                    case 14:
                        $letra = "BD";
                        break;
                    case 15:
                        $letra = "BE";
                        break;
                    case 16:
                        $letra = "BF";
                        break;
                    case 17:
                        $letra = "BG";
                        break;
                    case 18:
                        $letra = "BH";
                        break;
                    case 19:
                        $letra = "BI";
                        break;
                    case 20:
                        $letra = "BJ";
                        break;
                    case 21:
                        $letra = "BK";
                        break;
                    /*
                     * Lertra C
                     */
                    case 22:
                        $letra = "CA";
                        break;
                    case 23:
                        $letra = "CB";
                        break;
                    case 24:
                        $letra = "CC";
                        break;
                    case 25:
                        $letra = "CD";
                        break;
                    case 26:
                        $letra = "CE";
                        break;
                    case 27:
                        $letra = "CF";
                        break;
                    case 28:
                        $letra = "CG";
                        break;
                    case 29:
                        $letra = "CH";
                        break;
                    case 30:
                        $letra = "CI";
                        break;
                    case 31:
                        $letra = "CJ";
                        break;
                    case 32:
                        $letra = "CK";
                        break;
                    /*
                     * Lertra D
                     */
                    case 33:
                        $letra = "DA";
                        break;
                    case 34:
                        $letra = "DB";
                        break;
                    case 35:
                        $letra = "DC";
                        break;
                    case 36:
                        $letra = "DD";
                        break;
                    case 37:
                        $letra = "DE";
                        break;
                    case 38:
                        $letra = "DF";
                        break;
                    case 39:
                        $letra = "DG";
                        break;
                    case 40:
                        $letra = "DH";
                        break;
                    case 41:
                        $letra = "DI";
                        break;
                    case 42:
                        $letra = "DJ";
                        break;
                    case 43:
                        $letra = "DK";
                        break;
                    /*
                    * Lertra F
                    */
                    case 44:
                        $letra = "FA";
                        break;
                    case 45:
                        $letra = "FB";
                        break;
                    case 46:
                        $letra = "FC";
                        break;
                    case 47:
                        $letra = "FD";
                        break;
                    case 48:
                        $letra = "FE";
                        break;
                    case 49:
                        $letra = "FF";
                        break;
                    case 50:
                        $letra = "FG";
                        break;
                    case 51:
                        $letra = "FH";
                        break;
                    case 52:
                        $letra = "FI";
                        break;
                    case 53:
                        $letra = "FJ";
                        break;
                    case 54:
                        $letra = "CK";
                        break;

                    /*
                    * Lertra G
                    */
                    case 55:
                        $letra = "GA";
                        break;
                    case 56:
                        $letra = "GB";
                        break;
                    case 57:
                        $letra = "GC";
                        break;
                    case 58:
                        $letra = "GD";
                        break;
                    case 59:
                        $letra = "GE";
                        break;
                    case 60:
                        $letra = "GF";
                        break;
                    case 61:
                        $letra = "GG";
                        break;
                    case 62:
                        $letra = "GH";
                        break;
                    case 63:
                        $letra = "GI";
                        break;
                    case 64:
                        $letra = "GJ";
                        break;
                    case 65:
                        $letra = "GK";
                        break;
                    /*
                    * Lertra H
                    */
                    case 66:
                        $letra = "HA";
                        break;
                    case 67:
                        $letra = "HB";
                        break;
                    case 68:
                        $letra = "HC";
                        break;
                    case 69:
                        $letra = "HD";
                        break;
                    case 70:
                        $letra = "HE";
                        break;
                    case 71:
                        $letra = "HF";
                        break;
                    case 72:
                        $letra = "HG";
                        break;
                    case 73:
                        $letra = "HH";
                        break;
                    case 74:
                        $letra = "HI";
                        break;
                    case 75:
                        $letra = "HJ";
                        break;
                    case 76:
                        $letra = "HK";
                        break;
                    /*
                    * Lertra I
                    */
                    case 77:
                        $letra = "IA";
                        break;
                    case 78:
                        $letra = "IB";
                        break;
                    case 79:
                        $letra = "IC";
                        break;
                    case 80:
                        $letra = "ID";
                        break;
                    case 81:
                        $letra = "IE";
                        break;
                    case 82:
                        $letra = "IF";
                        break;
                    case 83:
                        $letra = "IG";
                        break;
                    case 84:
                        $letra = "IH";
                        break;
                    case 85:
                        $letra = "II";
                        break;
                    case 86:
                        $letra = "IJ";
                        break;
                    case 87:
                        $letra = "IK";
                        break;
                    /*
                    * Lertra J
                    */
                    case 88:
                        $letra = "JA";
                        break;
                    case 89:
                        $letra = "JB";
                        break;
                    case 90:
                        $letra = "JC";
                        break;
                    case 91:
                        $letra = "JD";
                        break;
                    case 92:
                        $letra = "JE";
                        break;
                    case 93:
                        $letra = "JF";
                        break;
                    case 94:
                        $letra = "JG";
                        break;
                    case 95:
                        $letra = "JH";
                        break;
                    case 96:
                        $letra = "JI";
                        break;
                    case 97:
                        $letra = "JJ";
                        break;
                    case 98:
                        $letra = "JK";
                        break;
                    /*
                    * Lertra K
                    */
                    case 99:
                        $letra = "KA";
                        break;
                    case 100:
                        $letra = "KB";
                        break;
                    case 101:
                        $letra = "KC";
                        break;
                    case 102:
                        $letra = "KD";
                        break;
                    case 103:
                        $letra = "KE";
                        break;
                    case 104:
                        $letra = "KF";
                        break;
                    case 105:
                        $letra = "KG";
                        break;
                    case 106:
                        $letra = "KH";
                        break;
                    case 107:
                        $letra = "KI";
                        break;
                    case 108:
                        $letra = "KJ";
                        break;
                    case 109:
                        $letra = "KK";
                        break;
                }
            }
            return $letra;
        }catch (\Exception $ex){
            throw $ex;
        }
    }

    public function guardarTurno($turno, $viaMostrador = false)
    {
        try {
            //Controlo que existan los datos del Turno
            if ($this->checkDatos($turno)) {
                //Controlo Disponibilidad sobre la Persona
                if ($this->disponibilidad->verificaTurnoSinConfirmarByPersona($turno->getCuit())) {

                    //Verifico que si es un Tipo de Tramite sin Turno
                    if($turno->getTipoTramite()->getSinTurno()){
                        $this->em->getConnection()->beginTransaction(); // suspend auto-commit
                        try {
                            //Seteo los valores del turno
                            $turno->setViaMostrador($viaMostrador);
                            $turno->setNumero($this->obtenerProximoTurnoSede($turno->getSede()->getId()));

                            //creo el asociado al turno comprobante y lo guardo
                            $comprobante = new Comprobante();
                            $comprobante->setTurno($turno);
                            $comprobante->setSede($turno->getSede()->getSede());
                            $comprobante->setLetra($turno->getSede()->getLetra());
                            $comprobante->setNumero($turno->getNumero());
                            $comprobante->setTipoTramite($turno->getTipoTramite()->getDescripcion());
                            $comprobante->setSecretKey($this->secret);
                            $this->em->persist($comprobante);

                            //relaciono el turno con el comprobante y guardo el turno
                            $turno->setComprobante($comprobante);
                            $this->em->persist($turno);

                            //confirmo los cambios
                            $this->em->flush();
                            $this->em->getConnection()->commit();

                            //Luego de confirmar los datos, envio el mail y guardo los cambios
                            //Creo el mail con los datos del turno y comprobante para guardarlo
                            $mail = new Mail();
                            $mail->setTextoMail($this->getCuerpoMail(1 /*Nuevo Turno*/));
                            $mail->setAsunto($this->formateTexto($turno, $mail->getTextoMail()->getAsunto()));
                            $mail->setTurno($turno);
                            $mail->setEmail($turno->getMail1());
                            $mail->setNombre($turno->getNombreApellido());
                            $mail->setTexto($this->formateTexto($mail->getTurno(), $mail->getTextoMail()->getTexto(),'',true));
                            $mail->setEnviado($this->sendEmail($mail));
                            if ($mail->getEnviado()) {
                                $mail->setFechaEnviado(new \DateTime("now"));
                            }
                            $this->em->persist($mail);
                            $this->em->flush();

                        } catch (Exception $e) {
                            $this->em->getConnection()->rollBack();
                            throw $e;
                        }

                    }else {
                        //Controlo Disponibilidad del Turno
                        $status = $this->disponibilidad->controlaDisponibilidad($turno->getFechaTurno(), $turno->getHoraTurno(), $turno->getTipoTramite()->getId(), $turno->getSede()->getId());
                        //Controlo como retorno la disponiblidad
                        if ($status['status']) {
                            $this->em->getConnection()->beginTransaction(); // suspend auto-commit
                            try {
                                //Seteo los valores del turno
                                $turno->setViaMostrador($viaMostrador);
                                $turno->setTurnoSede($status['data']);
                                $turno->setNumero($this->obtenerProximoTurnoSede($turno->getSede()->getId()));

                                //creo el asociado al turno comprobante y lo guardo
                                $comprobante = new Comprobante();
                                $comprobante->setTurno($turno);
                                $comprobante->setSede($turno->getSede()->getSede());
                                $comprobante->setLetra($turno->getSede()->getLetra());
                                $comprobante->setNumero($turno->getNumero());
                                $comprobante->setTipoTramite($turno->getTipoTramite()->getDescripcion());
                                $comprobante->setFecha($turno->getFechaTurno()->format('d/m/Y'));
                                $comprobante->setHora($turno->getHoraTurno()->format('h:i'));
                                $comprobante->setSecretKey($this->secret);
                                $this->em->persist($comprobante);

                                //relaciono el turno con el comprobante y guardo el turno
                                $turno->setComprobante($comprobante);
                                $this->em->persist($turno);


                                //confirmo los cambios
                                $this->em->flush();
                                $this->em->getConnection()->commit();

                                //Luego de confirmar los datos, envio el mail y guardo los cambios
                                //Creo el mail con los datos del turno y comprobante para guardarlo
                                $mail = new Mail();
                                $mail->setTextoMail($this->getCuerpoMail(1 /*Nuevo Turno*/));
                                $mail->setAsunto($this->formateTexto($turno, $mail->getTextoMail()->getAsunto()));
                                $mail->setTurno($turno);
                                $mail->setEmail($turno->getMail1());
                                $mail->setNombre($turno->getNombreApellido());
                                $mail->setTexto($this->formateTexto($mail->getTurno(), $mail->getTextoMail()->getTexto()));
                                $mail->setEnviado($this->sendEmail($mail));
                                if ($mail->getEnviado()) {
                                    $mail->setFechaEnviado(new \DateTime("now"));
                                }
                                $this->em->persist($mail);
                                $this->em->flush();

                            } catch (Exception $e) {
                                $this->em->getConnection()->rollBack();
                                throw $e;
                            }

                        } else {
                            $exp = new \Exception('Error 1.TM.GT No se encuentra la disponiblidad para la fecha: ' . $turno->getFechaTurno()->format('d/m/Y') . ' hora Turno: ' . $turno->getHoraTurno()->format('H:i'));
                            throw $exp;
                        }
                    }
                } else {
                    $exp = new \Exception('Error 1.TM.GT La persona tiene un turno sin confirmar o no cancelado. En el mail de la solicitud tiene la dirección para cancelar el turno');
                    throw $exp;
                }
            } else {
                $exp = new \Exception('Error 1.TM.GT datos enviados no concuerdan');
                throw $exp;
            }
            //OK
            return $turno;
        }catch (\Exception $ex){
            //throw new \Exception('Error 1.TM.GT No se encuentra la sede buscada');
            throw $ex;
        }
    }

    /**
     * Verfica que los datos del Turnos existan
     *
     * @param AdminBundle:Turno $turno
     *
     * @return boolean
     */
    private function checkDatos($turno)
    {
        //Controlo la sede
        $sede = $this->em->getRepository('AdminBundle:Sede')->findById($turno->getSede()->getId());
        if (is_null($sede)) {
            $exp = new \Exception('Error 1.TM.CD No se encuentra la sede');
            throw $exp;
        }
        //Controlo el tipo de tramite
        $tipoTramite = $this->em->getRepository('AdminBundle:TipoTramite')->findById($turno->getTipoTramite()->getId());
        if (is_null($tipoTramite)) {
            $exp = new \Exception('Error 1.TM.CD No se encuentra el tipo de tramite');
            throw $exp;
        }
        return true;
    }

    public function getComprobanteByHash($hash)
    {
        try {
            $comprobante = null;
            $texto = explode("$", Crypto::decrypt($hash, Key::loadFromAsciiSafeString($this->secret)));
            if (isset($texto[0])) {
                $comprobante = $this->em->getRepository('AdminBundle:Comprobante')->findOneBy(array('id' => $texto[0]));
            }
            return $comprobante;
        }catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex){
            $exp = new \Exception('Error 1.TM.GCBH No se encuentra el comprobante');
            throw $exp;
        }
    }

    public function cancelarTurno($cuit,$numeroTurno,$mostrador=false,$cancelacionMasiva=false,$turno=false){
        $turnos = array();
        try{

            if($turno==false) {
                $letra = (substr($numeroTurno, 0, 2));
                $numero = (substr($numeroTurno, 2, strlen($numeroTurno)));
                $repository = $this->em->getRepository('AdminBundle:Turno', 't')
                    ->createQueryBuilder('t')
                    ->innerJoin('AdminBundle:Sede', 's', 'WITH', 't.sede= s.id')
                    ->where('(t.numero = :numero) AND t.fechaCancelado iS NULL AND t.fechaConfirmacion IS NULL')
                        ->setParameter('numero', $numero)
                    ->andWhere('t.cuit = :cuit')
                        ->setParameter('cuit', $cuit)
                    ->andWhere('s.letra = :letra')
                        ->setParameter('letra', $letra);
                $turnos = $repository->getQuery()->getResult();
                if(count($turnos) == 0){
                    throw new \Exception('No se ha encontrado el Turnos.');
                }
            }else{
                $turnos[] = $turno;
            }
            foreach ($turnos as $turno){

                $turno->setFechaCancelado(new \DateTime("now"));

                $motivoCancelacion = '';

                if($cancelacionMasiva == false){
                    if($mostrador == false ){
                        $turno->setCanceladoWeb(true);
                        $turno->setCanceladoMostrador(false);
                    }else{
                        $turno->setCanceladoWeb(false);
                        $turno->setCanceladoMostrador(true);
                    }
                }else{
                    $motivoCancelacion = $cancelacionMasiva->getMotivo();
                    $turno->setCanceladoWeb(false);
                    $turno->setCanceladoMostrador(false);
                }

                $turno->setCanceladoMostrador($mostrador);

                $tipoMail = 2;
                if($cancelacionMasiva != null){
                    $turno->setCancelacionMasiva($cancelacionMasiva);
                    $tipoMail = 3;
                }
                $this->em->persist($turno);
                
                $mail = new Mail();
                $mail->setTextoMail($this->getCuerpoMail($tipoMail));
                $mail->setAsunto($this->formateTexto($turno, $mail->getTextoMail()->getAsunto()));
                $mail->setTurno($turno);
                $mail->setEmail($turno->getMail1());
                $mail->setNombre($turno->getNombreApellido());
                $mail->setTexto($this->formateTexto($mail->getTurno(), $mail->getTextoMail()->getTexto(),$motivoCancelacion));

                $mail->setEnviado($this->sendEmail($mail));
                if ($mail->getEnviado()) {
                    $mail->setFechaEnviado(new \DateTime("now"));
                }

                $this->em->persist($mail);
                $this->em->flush();
            }

        }catch (\Exception $e){
            throw $e;
        }

        return $turno->getComprobante();

    }

    public function getCuerpoMail($tipoEnvio)
    {
        try {
            $textoMail = null;
            if ($tipoEnvio == 1) {
                $textoMail = $this->em->getRepository('AdminBundle:TextoMail')->findOneByAccion('nuevo');
            } elseif ($tipoEnvio == 2) {
                $textoMail = $this->em->getRepository('AdminBundle:TextoMail')->findOneByAccion('cancelado');
            } else {
                $textoMail = $this->em->getRepository('AdminBundle:TextoMail')->findOneByAccion('cancelado_masivo');
            }
            return $textoMail;
        }catch (\Exception $e){
            $exp = new \Exception('Error 1.TM.GCM No se encuentra el TextoMail solicitado');
            throw $exp;
        }
    }

    public function sendEmail($mail)
    {
        try {
            $message = \Swift_Message::newInstance()
                ->setSubject($mail->getAsunto())
                ->setFrom($this->emailFrom)
                ->setTo($mail->getEmail())
                ->setBody(html_entity_decode($mail->getTexto()), 'text/html');

            if ($this->mailer->send($message) == 1) {
                return true;
            } else {
                return false;
            }
        }catch (\Exception $e){
            $exp = new \Exception('Error 2.TM.SE No se pudo enviar el mail');
            throw $exp;
        }
    }

    private function formateTexto($turno, $texto,$motivoCancelacionMasiva = '', $sinTurno = false)
    {
        $turno->setHashComprobante($this->secret);
        $hora= '';
        $fecha='';
        if($sinTurno == false){
            if($turno->getFechaTurno()){
                $fecha = $turno->getFechaTurno()->format('d/m/Y');
                $hora = $turno->getHoraTurno()->format('H:i');
            }
        }
        return  str_replace('%MOTIVO_CANCELACION_MASIVA%', $motivoCancelacionMasiva,
                    str_replace('%LINK_CANCELACION%',$_SERVER['SERVER_NAME'].$this->router->generate('cancelar_turno',array('hash'=>$turno->getComprobante()->getHash())),//,UrlGeneratorInterface::ABSOLUTE_URL),
                        str_replace('%LINK_COMPRBANTE%',
                            $_SERVER['SERVER_NAME'].$this->router->generate('generar_comprobante', array('hash' => $turno->getComprobante()->getHash())),
                            str_replace('%DIRECCION%', $turno->getSede()->getDireccion(),
                                str_replace('%SEDE%', $turno->getSede()->getSede(),
                                    str_replace('%CUIT%', $turno->getCuit(),
                                        str_replace('%HORA_TURNO%', $hora,
                                            str_replace('%FECHA_TURNO%', $fecha,
                                                str_replace('%NUMERO_TURNO%', $turno->getSede()->getLetra() . '-' . $turno->getNumero(),
                                                    str_replace('%NOMBRE_PERSONA%', $turno->getNombreApellido(), $texto)
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
        ;
    }

    public function cancelarTurnsoMasiva($cancelacionMasiva){

        $repository = $this->em->getRepository('AdminBundle:Turno', 't')
            ->createQueryBuilder('t')
            ->where('(t.fechaTurno = :fecha) AND t.fechaCancelado iS NULL')->setParameter('fecha', $cancelacionMasiva->getFecha()->format('Y/m/d'))
            ->andWhere('t.sede = :sede')->setParameter('sede', $cancelacionMasiva->getSede()->getId());
        $turnos = $repository->getQuery()->getResult();
        foreach ($turnos as $turno){
            $this->cancelarTurno(null,null,false,$cancelacionMasiva,$turno);
        }
    }

    /**
     * Obtener turnos para la exportacion
     *
     * @param array $sedeId
     * @param time $horaDesde
     * @param time $horaHasta
     * @param array $estado
     * @param array $tipoTramite
     * @param date $fechaDesde
     * @param date $fechaHasta
     * @param string $cuit
     * @param integer $nroTurno
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function obtenerExportacion($sedeId, $horaDesde, $horaHasta, $estados, $tipoTramite, $fechaDesde, $fechaHasta, $cuit=null, $nroTurno=null)
    {
        try {
            $fechaDesde = date("Y/m/d", mktime(0, 0, 0, substr($fechaDesde, 3, 2), substr($fechaDesde, 0, 2), substr($fechaDesde, 6, 4)));
            $fechaHasta = date("Y/m/d", mktime(0, 0, 0, substr($fechaHasta, 3, 2), substr($fechaHasta, 0, 2), substr($fechaHasta, 6, 4)));

            $repository = $this->em->getRepository('AdminBundle:Turno', 'p');
            $repository = $repository->createQueryBuilder('p');

            //Obtengo las horas y minutos
            $hora = 0;
            $min = 0;
            $min = 0;
            if (strlen($horaDesde) == 7) {
                $hora = (substr($horaDesde, 0, 1));
                $min = (substr($horaDesde, 2, 2));
                if (substr($horaDesde, 5, 2) == 'PM') {
                    if ($hora != 12) {
                        $hora = $hora + 12;
                    }
                }
            } else {
                $hora = (substr($horaDesde, 0, 2));
                $min = (substr($horaDesde, 3, 2));
                if (substr($horaDesde, 6, 2) == 'PM') {
                    if ($hora != 12) {
                        $hora = $hora + 12;
                    }
                }
            }
            $hora2 = 0;
            $min2 = 0;
            if (strlen($horaHasta) == 7) {
                $hora2 = (substr($horaHasta, 0, 1));
                $min2 = (substr($horaHasta, 2, 2));
                if (substr($horaHasta, 5, 2) == 'PM') {
                    if ($hora2 != 12) {
                        $hora = $hora2 + 12;
                    }
                }
            } else {
                $hora2 = (substr($horaHasta, 0, 2));
                $min2 = (substr($horaHasta, 3, 2));
                if (substr($horaHasta, 6, 2) == 'PM') {
                    if ($hora2 != 12) {
                        $hora2 = $hora2 + 12;
                    }
                }
            }

            $repository->where('p.horaTurno >= :horaDesde AND p.horaTurno  <=  :horaHasta')
                ->setParameter('horaDesde', ($hora . ':' . $min . ':00'))
                ->setParameter('horaHasta', ($hora2 . ':' . $min2) . ':00');


            $arraySede = array();
            foreach ($sedeId as $sede) {
                $arraySede[] = $sede->getId();
            }
            $repository->andWhere('p.sede IN (:sedeId)')->setParameter('sedeId', $arraySede);

            $repository->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')->setParameter('fecha_turno_desde', $fechaDesde . ' 00:00:00')->setParameter('fecha_turno_hasta', $fechaHasta . ' 23:59:59');

            $arrayTipoTramite = array();
            $noTodosLosTramites = true;
            foreach ($tipoTramite as $tipoTramiteId) {
                if ($tipoTramiteId == 0) {
                    $noTodosLosTramites = false;
                } else {
                    $arrayTipoTramite[] = $tipoTramiteId;
                }
            }
            if ($noTodosLosTramites) {
                $repository->andWhere('p.tipoTramite IN (:tipoTramite)')->setParameter('tipoTramite', $arrayTipoTramite);
            }

            if ($cuit) {
                $repository->andWhere('p.cuit = :cuit')->setParameter('cuit', $cuit);
            }

            if ($nroTurno) {
                $repository->andWhere('p.numero = :numero')->setParameter('numero', $nroTurno);
            }

            $indistinto = false;
            $str = '';
            $primero = true;
            $conDqlBusquedaAtendido = false;
            $conDqlBusquedaNoAtendido = false;
            foreach ($estados as $estado) {
                if ($estado < 0) {
                    $indistinto = true;
                }
                //Estado Sin Corfirmar
                if ($estado == 0 OR $indistinto == true) {
                    if ($primero) {
                        $str = '(p.fechaConfirmacion IS NULL AND p.fechaCancelado IS NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaConfirmacion IS NULL AND p.fechaCancelado IS NULL)';
                    }
                }
                //Estado Confirmados
                if ($estado == 1 OR $indistinto == true) {
                    if ($primero) {
                        $str = '(p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL)';
                    }
                }
                //Estado Confirmados Sin Turnos
                if ($estado == 2 OR $indistinto == true) {
                    if ($primero) {
                        $str = '(p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL)';
                    }
                }
                //Estado Confirmados Con Turnos
                if ($estado == 3 OR $indistinto == true) {
                    if ($primero) {
                        $str = '(p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL)';
                    }
                }
                //Estado Atendidos
                if ($estado == 4 OR $indistinto == true) {
                    $conDqlBusquedaAtendido = true;
                    if ($primero) {
                        $str = '(p.fechaCancelado IS NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaCancelado IS NULL)';
                    }
                }
                //Estado Atendidos Sin Turnos
                if ($estado == 5 OR $indistinto == true) {
                    $conDqlBusquedaAtendido = true;
                    if ($primero) {
                        $str = '(p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL)';
                    }
                }
                //Estado Atendidos Con Turnos
                if ($estado == 6 OR $indistinto == true) {
                    $conDqlBusquedaAtendido = true;
                    if ($primero) {
                        $str = '(p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL)';
                    }
                }
                //Estado Confirmados y no Atendido
                if ($estado == 7 OR $indistinto == true) {
                    if ($primero) {
                        $str = '(p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL)';
                    }
                    $conDqlBusquedaNoAtendido = true;
                }
                //Estado Cancelados
                if ($estado == 8 OR $indistinto == true) {
                    if ($primero) {
                        $str = '(p.fechaCancelado IS NOT NULL)';
                        $primero = false;
                    } else {
                        $str = $str . ' OR (p.fechaCancelado IS NOT NULL)';
                    }
                }
            }
            $repository->andWhere($str);

            if ($indistinto == false) {
                if ($conDqlBusquedaAtendido == true OR $conDqlBusquedaNoAtendido == true) {
                    $sub = $this->em->createQueryBuilder();
                    $sub->select("t");
                    $sub->from("AdminBundle:ColaTurno", "t");
                    if ($conDqlBusquedaAtendido == true AND $conDqlBusquedaNoAtendido == false) {
                        $sub->andWhere('t.turno = p.id AND t.atendido = true');
                    } else if ($conDqlBusquedaAtendido == false AND $conDqlBusquedaNoAtendido == true) {
                        $sub->andWhere('t.turno = p.id AND t.atendido = false');
                    } else {
                        $sub->andWhere('t.turno = p.id');
                    }

                    $repository->andWhere($repository->expr()->exists($sub->getDQL()));


                }
            }

            $repository->orderBy('p.fechaTurno', 'ASC');
            $repository->orderBy('p.horaTurno', 'ASC');


            return $repository->getQuery()->getResult();
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function primerTurno($turno){
        $fechaDesdeDate =  new \DateTime($turno->getFechaTurno()->format('Y-m-d').' 00:00:00');
        $fechaHastaDate = new \DateTime($turno->getFechaTurno()->format('Y-m-d').' 23:59:59');

        $consulta =  $this->em->createQueryBuilder();
        $consulta->select("ct");
        $consulta->from("AdminBundle:ColaTurno", "ct");
        $consulta->innerJoin('AdminBundle:Turno','t', 'WITH','ct.turno = t.id');


//        $consulta->andWhere('t.fechaTurno > :fechaTurnoDesde')
//            ->setParameter('fechaTurnoDesde',$fechaDesdeDate);
//        $consulta->andWhere('t.fechaTurno < :fechaTurnoHasta')
//            ->setParameter('fechaTurnoHasta',$fechaHastaDate);

        $consulta->andWhere('t.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
            ->setParameter('fecha_turno_desde', $fechaDesdeDate )
            ->setParameter('fecha_turno_hasta', $fechaHastaDate );

        $resultado = $consulta->getQuery()->getResult();
        if(count($resultado) > 1){
            return false;
        }else{
            return true;
        }
    }

    public function buscarTurnoPorNumeroYTurnoSede($numero,$turnoSede,$idTurno){
        $consulta =  $this->em->createQueryBuilder();
        $consulta->select("t");
        $consulta->from("AdminBundle:Turno", "t");
        $consulta->innerJoin("AdminBundle:ColaTurno","ct","WITH","ct.turno = t.id");
        $consulta->where('t.turnoSede = :turnoSede')->setParameter('turnoSede',$turnoSede->getId());
        $consulta->andWhere('ct.numero = :numero')->setParameter('numero',$numero);
        $consulta->andWhere('t.id = :id')->setParameter('id',$idTurno);
        return $consulta->getQuery()->getResult();

    }

    public function marcarAtendidoTurno($turno){
        $cola = $turno->getColaTurno();
        $cola = $cola[0];
        $cola->setAtendido(true);
        $this->em->persist($cola);
        $this->em->flush();
    }

    public function marcarLlamadoTurno($turno, $box, $usuario)
    {
        $cola = $turno->getColaTurno();
        $colaS = $cola[0];

        $colaSave = $this->em
            ->getRepository('AdminBundle:ColaTurno')
            ->findOneBy(array('letra' => $colaS->getLetra(), 'numero' => $colaS->getNumero()));

        $boxs= $this->em
            ->getRepository('AdminBundle:Box')
            ->findOneBy(array('descripcion' => $box->getDescripcion()));

        $colaSave->setLlamado(true);
        $colaSave->setFechaLlamado(new \DateTime("now"));
        $colaSave->setUsuarioAtendido($usuario);
        $colaSave->setBox($boxs);


        $this->em->persist($colaSave);
        $this->em->flush();
    }

}
