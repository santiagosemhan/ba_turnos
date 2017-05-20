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

    private $emailFrom = 'mail@milentar.com';

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
        $repository->where('(p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta) AND p.fechaCancelado IS NULL ')->setParameter('fecha_turno_desde', $fecha.' 00:00:00')->setParameter('fecha_turno_hasta', $fecha.' 23:59:59');
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
            $repository->andWhere('p.cuit > :cuit')->setParameter('cuit', $cuit);
        }

        if ($nroTurno) {
            $repository->andWhere('p.numero > :numero')->setParameter('numero', $nroTurno);
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
                WHERE t.sede = :sedeId AND t.viaMostrador = true AND (t.fechaTurno BETWEEN :desde AND :hasta)'
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
        $repository = $this->em->getRepository('AdminBundle:Sede');
        $sede = $repository->findOneById($sedeId);
        $sede->setUltimoTurno($numeroTurno);
        $this->em->persist($sede);
        $this->em->flush();
        return true;
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
        $repository = $this->em->getRepository('AdminBundle:Sede');
        $sede = $repository->findOneById($sedeId);
        $proximoNumero = $sede->getUltimoTurno()+1;
        $sede->setUltimoTurno($proximoNumero);
        $this->em->persist($sede);
        $this->em->flush();
        return $proximoNumero;
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

            //Determino como debo determinar la Letra y numero del turno
            if($turno->getViaMostrador() == false) {

                $turnoSede = $turno->getTurnoSede();
                //array con la cantidad de turnos que puede asignar cada turnoSede
                $turnoSedeCantidadTurnos = array();

                //determino la letra que corresponde en base a los turnoSede
                $repositoryTS = $this->em->getRepository('AdminBundle:TurnoSede')->createQueryBuilder('ts')
                    ->where('ts.sede = :sedeId AND ts.activo = true ')->setParameter('sedeId', $turnoSede->getSede()->getid())
                    ->orderBy('ts.id');
                $turnosSede = $repositoryTS->getQuery()->getResult();
                $turnoSedeIndiceLetra =0;
                $indice =0;
                //determino cual cual turnoSede es para asegnarle la letra
                foreach($turnosSede as $turnoSedeO){
                    if($turnoSede->getId() == $turnoSedeO->getId() ){
                        $turnoSedeIndiceLetra = $indice;
                    }
                    $cantidadTurnosSegudo = 1;
                    $horaDesde = $turnoSedeO->getHoraTurnosDesde();
                    $horaHasta = $turnoSedeO->getHoraTurnosHasta();
                    $horasTurno = $horaHasta->diff($horaDesde);
                    $difHoras = intval($horasTurno->format('%H'));
                    $difMinutos = intval($horasTurno->format('%i'));
                    $difMinutos = $difMinutos + ($difHoras * 60);
                    $cantidadTurnos = ($difMinutos / $turnoSedeO->getCantidadFrecuencia());
                    $turnoSedeCantidadTurnos[$turnoSedeO->getId()] = $cantidadTurnos;
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
                $turnosDeldia = $this->disponibilidad->getCantidadHoraTurno($tipoTramite, $turnoSede, $turnosDeldia, $dia, $mes, $anio, $diaActual,true);

                if(count($turnosDeldia)>0){
                    //en base a la cantidad determino el numero del turno en base la distribucion de turnos que existe.
                    $cantidad = 0;
                    $numeroTurno = 1;
                    foreach ($turnosDeldia as $indice => $turnoDeldia) {

                        if(($indice == $turno->getHoraTurno()->format('H:i')  )){
                            //todo determinar si existe ya un turno con este valor (caso que se determine mas de un turno por hora)
                            $fecha = $turno->getFechaTurno()->format('Y/m/d');
                            $repository = $this->em->getRepository('AdminBundle:ColaTurno', 'p')->createQueryBuilder('p');
                            $repository->innerJoin('AdminBundle:Turno', 't', 'WITH', 'p.turno = t.id');
                            $repository->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 't.turnoSede = ts.id');
                            $repository->where('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                                ->setParameter('fecha_turno_desde', $fecha . ' 00:00:00')
                                ->setParameter('fecha_turno_hasta', $fecha . ' 23:59:59')
                                ->andWhere('t.horaTurno = :horaTurno')
                                ->setParameter('horaTurno',$turno->getHoraTurno())
                                ->andWhere('p.sede = :sedeId')
                                ->setParameter('sedeId', $cola->getSede()->getId());

                            $cantidadCola = count($repository->getQuery()->getResult());
                            //Calculo el proximo numero
                            $numeroTurno = $cantidad+$cantidadCola+1;
                        }else{
                            $cantidad = $cantidad + ($turnoDeldia);
                        }

                    }

                    //controla si los turnoSede  ocupan mas de una letra
                    $ultimo = false;
                    foreach($turnoSedeCantidadTurnos as $indicesTurno => $turnoSedeCantidadTurno){
                        if($indicesTurno == $turnoSede->getId()){
                            $ultimo = true;
                        }
                        if($ultimo == false){
                            //Controlo si un turnoSede da mas que las dos convinaciones de letras
                            if($turnoSedeCantidadTurno > 1078){
                                $saltosLentras = intdiv ( $turnoSedeCantidadTurno, 1078);
                                $turnoSedeIndiceLetra = $turnoSedeIndiceLetra+$saltosLentras;
                            }
                        }
                    }

                    //salto a la siguiente primera letra
                    $turnoSedeIndiceLetra = $turnoSedeIndiceLetra *11;

                    //Determino la letra a asignarse en base a la cantidad de turnosSede y el numero de Turno
                    //Determino el desafase que genera el numero de turno y cuantos saltos de convisiones de las letras existe
                    $saltosLentras = intdiv ( $numeroTurno, 98);
                    //Determino si existen saltos
                    if($saltosLentras> 0){
                        //calculo el numero numero del turno por vuelve a comenzar el turno
                        $numeroTurno = $numeroTurno % 98;
                        //si el numero de turno calculado es 0 le asigno el 1 para comenzar
                        if($numeroTurno == 0){
                            $numeroTurno =1;
                        }
                        $turnoSedeIndiceLetra = $turnoSedeIndiceLetra +$saltosLentras;
                    }

                    $cola->setLetra($this->obtenerLetra($turnoSedeIndiceLetra, $prioritario));
                    $cola->setNumero($numeroTurno);
                }else{
                    throw new \Exception('No se ha encontrado Turnos disponibles. Verifique que la Hora del Turno no haya pasado. Disculpe las molestias');
                }
            }else {

                $fecha = $turno->getFechaTurno()->format('Y/m/d');
                $repository = $this->em->getRepository('AdminBundle:ColaTurno', 'p')->createQueryBuilder('p');
                $repository->where('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')
                    ->setParameter('fecha_turno_desde', $fecha . ' 00:00:00')
                    ->setParameter('fecha_turno_hasta', $fecha . ' 23:59:59');
                $repository->andWhere('p.sede = :sedeId')->setParameter('sedeId', $cola->getSede()->getId());
                if ($prioritario) {
                    $repository->andWhere('p.prioritario = false');
                } else {
                    $repository->andWhere('p.prioritario = false');
                }
                //cuento cuantos turnso ya entregue para ese dia
                $cantidad = count($repository->getQuery()->getResult());
                if ($cantidad > 0) {
                    $numero = $cantidad % 100;
                    $cantidad = intdiv($cantidad, 100);
                    if ($resto = 99) {
                        $cantidad = $cantidad + 1;
                        $numero = 1;
                    } else {
                        $numero = $numero + 1;
                    }
                } else {
                    $cantidad = 0;
                    $numero = 1;
                }
                //obtengo la letra
                $cola->setLetra($this->obtenerLetra($cantidad, $prioritario));
                $cola->setNumero($numero);

            }
            $this->em->persist($cola);
            $this->em->flush();
            $this->em->getConnection()->commit();

        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        return true;
    }

    private function obtenerLetra($cantidad, $reservado)
    {
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
    }

    public function guardarTurno($turno)
    {
        //Controlo que existan los datos del Turno
        if ($this->checkDatos($turno)) {
            //Controlo Disponibilidad sobre la Persona
            if ($this->disponibilidad->verificaTurnoSinConfirmarByPersona($turno->getCuit())) {
                //Controlo Disponibilidad del Turno
                $status = $this->disponibilidad->controlaDisponibilidad($turno->getFechaTurno(), $turno->getHoraTurno(), $turno->getTipoTramite()->getId(), $turno->getSede()->getId());
                //Controlo como retorno la disponiblidad
                if ($status['status']) {
                    $this->em->getConnection()->beginTransaction(); // suspend auto-commit
                    try {
                        //Seteo los valores del turno
                        $turno->setViaMostrador(false);
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
                    $exp = new Exception('No se encuentra la disponiblidad para la fecha: ' . $turno->getFechaTurno()->format('d/m/Y') . ' hora Turno: ' . $turno->getHoraTurno()->format('H:i'));
                    throw $exp;
                }
            } else {
                $exp = new Exception('La persona tiene un turno sin confirmar o no cancelado');
                throw $exp;
            }
        } else {
            $exp = new Exception('Los datos enviados no concuerdan');
            throw $exp;
        }
        //OK
        return $turno;
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
            $exp = new Exception('No se encuentra la sede');
            throw $exp;
        }
        //Controlo el tipo de tramite
        $tipoTramite = $this->em->getRepository('AdminBundle:TipoTramite')->findById($turno->getTipoTramite()->getId());
        if (is_null($tipoTramite)) {
            $exp = new Exception('No se encuentra el tipo de tramite');
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
            return null;
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
        $textoMail = null;
        if ($tipoEnvio == 1) {
            $textoMail = $this->em->getRepository('AdminBundle:TextoMail')->findOneByAccion('nuevo');
        } elseif ($tipoEnvio == 2) {
            $textoMail = $this->em->getRepository('AdminBundle:TextoMail')->findOneByAccion('cancelado');
        } else {
            $textoMail = $this->em->getRepository('AdminBundle:TextoMail')->findOneByAccion('cancelado_masivo');
        }
        return $textoMail;
    }

    public function sendEmail($mail)
    {
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
    }

    private function formateTexto($turno, $texto,$motivoCancelacionMasiva = '')
    {
        $turno->setHashComprobante($this->secret);
        return  str_replace('%MOTIVO_CANCELACION_MASIVA%', $motivoCancelacionMasiva,
                    str_replace('%LINK_CANCELACION%','',//$this->router->generate('cancelar_turno',array('turno'=>$turno),UrlGeneratorInterface::ABSOLUTE_URL),
                        str_replace('%LINK_COMPRBANTE%',
                            $_SERVER['SERVER_NAME'].$this->router->generate('generar_comprobante', array('hash' => $turno->getComprobante()->getHash())),
                            str_replace('%DIRECCION%', $turno->getSede()->getDireccion(),
                                str_replace('%SEDE%', $turno->getSede()->getSede(),
                                    str_replace('%CUIT%', $turno->getCuit(),
                                        str_replace('%HORA_TURNO%', $turno->getHoraTurno()->format('H:i'),
                                            str_replace('%FECHA_TURNO%', $turno->getFechaTurno()->format('d/m/Y'),
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
        $fechaDesde = date("Y/m/d", mktime(0, 0, 0, substr($fechaDesde, 3, 2), substr($fechaDesde, 0, 2), substr($fechaDesde, 6, 4)));
        $fechaHasta = date("Y/m/d", mktime(0, 0, 0, substr($fechaHasta, 3, 2), substr($fechaHasta, 0, 2), substr($fechaHasta, 6, 4)));

        $repository = $this->em->getRepository('AdminBundle:Turno', 'p');
        $repository = $repository->createQueryBuilder('p');

        //Obtengo las horas y minutos
        $hora=0;
        $min=0;
        $min=0;
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
        $hora2=0;
        $min2=0;
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
            ->setParameter('horaDesde', ($hora.':'.$min.':00'))
            ->setParameter('horaHasta', ($hora2.':'.$min2).':00');


        $arraySede = array();
        foreach($sedeId as $sede){
            $arraySede[] = $sede->getId();
        }
        $repository->andWhere('p.sede IN (:sedeId)')->setParameter('sedeId', $arraySede);

        $repository->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')->setParameter('fecha_turno_desde', $fechaDesde.' 00:00:00')->setParameter('fecha_turno_hasta', $fechaHasta.' 23:59:59');

        $arrayTipoTramite = array();
        $noTodosLosTramites = true;
        foreach ($tipoTramite as $tipoTramiteId){
            if( $tipoTramiteId == 0 ){
                $noTodosLosTramites = false;
            }else{
                $arrayTipoTramite[]=$tipoTramiteId;
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
        $str  ='';
        $primero = true;
        $conDqlBusquedaAtendido = false;
        $conDqlBusquedaNoAtendido = false;
        foreach($estados as $estado){
            if($estado < 0){
                $indistinto  = true;
            }
            //Estado Sin Corfirmar
            if($estado == 0 OR $indistinto == true){
                if($primero){
                    $str ='(p.fechaConfirmacion IS NULL AND p.fechaCancelado IS NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaConfirmacion IS NULL AND p.fechaCancelado IS NULL)';
                }
            }
            //Estado Confirmados
            if($estado == 1 OR $indistinto == true){
                if($primero){
                    $str ='(p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL)';
                }
            }
            //Estado Confirmados Sin Turnos
            if($estado == 2 OR $indistinto == true){
                if($primero){
                    $str ='(p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL)';
                }
            }
            //Estado Confirmados Con Turnos
            if($estado == 3  OR $indistinto == true){
                if($primero){
                    $str ='(p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL)';
                }
            }
            //Estado Atendidos
            if($estado == 4 OR $indistinto == true){
                $conDqlBusquedaAtendido = true;
                if($primero){
                    $str ='(p.fechaCancelado IS NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaCancelado IS NULL)';
                }
            }
            //Estado Atendidos Sin Turnos
            if($estado == 5 OR $indistinto == true){
                $conDqlBusquedaAtendido = true;
                if($primero){
                    $str ='(p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true AND p.fechaCancelado IS NULL)';
                }
            }
            //Estado Atendidos Con Turnos
            if($estado == 6 OR $indistinto == true){
                $conDqlBusquedaAtendido = true;
                if($primero){
                    $str ='(p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false AND p.fechaCancelado IS NULL)';
                }
            }
            //Estado Confirmados y no Atendido
            if($estado == 7 OR $indistinto == true){
                if($primero){
                    $str ='(p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaConfirmacion IS NOT NULL AND p.fechaCancelado IS NULL)';
                }
                $conDqlBusquedaNoAtendido = true;
            }
            //Estado Cancelados
            if($estado == 8  OR $indistinto == true){
                if($primero){
                    $str ='(p.fechaCancelado IS NOT NULL)';
                    $primero = false;
                }else{
                    $str = $str.' OR (p.fechaCancelado IS NOT NULL)';
                }
            }
        }
        $repository->andWhere($str);

        if($indistinto == false){
            if($conDqlBusquedaAtendido == true OR $conDqlBusquedaNoAtendido == true){
                $sub =  $this->em->createQueryBuilder();
                $sub->select("t");
                $sub->from("AdminBundle:ColaTurno", "t");
                if($conDqlBusquedaAtendido == true AND $conDqlBusquedaNoAtendido == false){
                    $sub->andWhere('t.turno = p.id AND t.atendido = true');
                }else if($conDqlBusquedaAtendido == false AND $conDqlBusquedaNoAtendido == true){
                    $sub->andWhere('t.turno = p.id AND t.atendido = false');
                }else{
                    $sub->andWhere('t.turno = p.id');
                }

                $repository->andWhere($repository->expr()->exists($sub->getDQL()));


            }
        }

        $repository->orderBy('p.fechaTurno', 'ASC');
        $repository->orderBy('p.horaTurno', 'ASC');


        return  $repository->getQuery()->getResult();
    }

    public function primerTurno($turno){
        $consulta =  $this->em->createQueryBuilder();
        $consulta->select("ct");
        $consulta->from("AdminBundle:ColaTurno", "ct");
        $consulta->innerJoin('AdminBundle:Turno','t', 'WITH','ct.turno = t.id');
        $consulta->where('t.turnoSede = :turnoSede')->setParameter('turnoSede',$turno->getTurnoSede());
        $consulta->andWhere('t.fechaTurno = :fechaTurno')->setParameter('fechaTurno',$turno->getFechaTurno());

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

}
