<?php


namespace AdminBundle\Services;

use AdminBundle\Entity\ColaTurno;
use AdminBundle\Entity\Turnos;
use Doctrine\ORM\EntityManager;

class TurnosManager
{

    private $em;
    private $disponibilidad;

    public function __construct(EntityManager $em, DisponibilidadManager $disponibilidad)
    {
        $this->em = $em;
        $this->disponibilidad = $disponibilidad;
    }

    /**
     * Obtener turnos sin confirmador
     *
     * @param interger $sedeId
     * @param date $fecha
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function obtenerTodosSinConfimar($sedeId,$fecha){
        $repository = $this->em->getRepository('AdminBundle:Turno','p')->createQueryBuilder('p');
        $repository->where('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')->setParameter('fecha_turno_desde', $fecha.' 00:00:00')->setParameter('fecha_turno_hasta', $fecha.' 23:59:59');
        $repository->andWhere('p.sede = :sedeId')->setParameter('sedeId', $sedeId);
        $repository->orderBy('p.horaTurno', 'ASC');
        return  $repository->getQuery()->getResult();
    }

    /**
     * Obtener turnos via filtro
     *
     * @param interger $sedeId
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
    public function obtenerPorFiltro($sedeId,$horaDesde,$horaHasta,$estado,$tipoTramite,$fecha,$cuit=null,$nroTurno=null)
    {
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha,3,2), substr($fecha,0,2), substr($fecha,6,4)));

        $repository = $this->em->getRepository('AdminBundle:Turno','p');
        $repository = $repository->createQueryBuilder('p');

        $hora = (substr($horaDesde,0,2)); $min = (substr($horaDesde,3,2));
        if(substr($horaDesde,6,2) == 'PM'){ $hora = $hora +12; }
        $hora2 = (substr($horaHasta,0,2)); $min2 = (substr($horaHasta,3,2));
        if(substr($horaHasta,6,2) == 'PM'){ $hora2 = $hora2 +12; }

        $repository->where('p.horaTurno >= :horaDesde AND p.horaTurno  <=  :horaHasta')
            ->setParameter('horaDesde', ($hora.':'.$min.':00'))
            ->setParameter('horaHasta', ($hora2.':'.$min2).':00');

        $repository->andWhere('p.sede = :sedeId')->setParameter('sedeId', $sedeId);

        $repository->andWhere('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')->setParameter('fecha_turno_desde', $fecha.' 00:00:00')->setParameter('fecha_turno_hasta', $fecha.' 23:59:59');

        if($tipoTramite != 0){
            $repository->andWhere('p.tipoTramite = :tipoTramite')->setParameter('tipoTramite', $tipoTramite);
        }

        if($cuit) {
            $repository->andWhere('p.cuit > :cuit')->setParameter('cuit', $cuit);
        }

        if($nroTurno) {
            $repository->andWhere('p.numero > :numero')->setParameter('numero', $nroTurno);
        }

        $sub =  $this->em->createQueryBuilder();
        $sub->select("t");
        $sub->from("AdminBundle:ColaTurno","t");
        $sub->andWhere('t.turno = p.id AND t.atendido = true');

        switch ($estado) {
            case 0: //Sin Corfirmar
                $repository->andWhere('p.fechaConfirmacion IS NULL');
                break;
            case 1: //Confirmados
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL');
                break;
            case 2: //Confirmados Sin Turnos
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true');
                break;
            case 3: //Confirmados Con Turnos
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false');
                break;
            case 4: //Atendidos
                $repository->andWhere($repository->expr()->exists($sub->getDQL()));
                break;
            case 5: //Atendidos Sin Turnos
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = true');
                $repository->andWhere($repository->expr()->exists($sub->getDQL()));
                break;
            case 6: //Atendidos Con Turnos
                $repository->andWhere('p.fechaConfirmacion IS NOT NULL AND p.viaMostrador = false');
                $repository->andWhere($repository->expr()->exists($sub->getDQL()));
                break;
            case 7: //Confirmados y no Atendido

                $sub =  $this->em->createQueryBuilder();
                $sub->select("t");
                $sub->from("AdminBundle:ColaTurno","t");
                $sub->andWhere('t.turno = p.id AND t.atendido = false');

                $repository->andWhere('p.fechaConfirmacion IS NOT NULL');
                $repository->andWhere($repository->expr()->exists($sub->getDQL()));
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
    public function getCantidad($sedeId,$fecha){
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha,3,2), substr($fecha,0,2), substr($fecha,6,4)));
        $query =  $this->em->createQuery(
            'SELECT count(t.id) cant
                FROM AdminBundle:Turno t
                WHERE t.sede = :sedeId AND (t.fechaTurno BETWEEN :desde AND :hasta)'
        )->setParameter('desde',$fecha.' 00:00:00')
            ->setParameter('hasta',$fecha.' 23:59:59')
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
    public function getCantidadConfirmados($sedeId,$fecha){
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha,3,2), substr($fecha,0,2), substr($fecha,6,4)));
        $query =  $this->em->createQuery(
                'SELECT count(t.id) cant
                FROM AdminBundle:Turno t
                WHERE t.sede = :sedeId AND t.fechaConfirmacion IS NOT NULL AND (t.fechaTurno BETWEEN :desde AND :hasta)'
        )->setParameter('desde',$fecha.' 00:00:00')
        ->setParameter('hasta',$fecha.' 23:59:59')
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
    public function getCantidadSinTurnos($sedeId,$fecha){
        $fecha = date("Y/m/d", mktime(0, 0, 0, substr($fecha,3,2), substr($fecha,0,2), substr($fecha,6,4)));
        $query =  $this->em->createQuery(
            'SELECT count(t.id) cant
                FROM AdminBundle:Turno t
                WHERE t.sede = :sedeId AND t.viaMostrador = true AND (t.fechaTurno BETWEEN :desde AND :hasta)'
        )->setParameter('desde',$fecha.' 00:00:00')
            ->setParameter('hasta',$fecha.' 23:59:59')
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
    public function actualizaNumeroTurnoSede($sedeId,$numeroTurno)
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
    public function confirmarTurno($turno,$user,$prioritario)
    {
        $turno->setUsuarioConfirmacion($user);
        $turno->setFechaConfirmacion(new \DateTime("now"));
        $this->em->persist($turno);
        $this->em->flush();

        $cola = new ColaTurno();
        $cola->setSede($turno->getSede());
        $cola->setTurno($turno);
        $cola->setPrioritario($prioritario);
        $cola->setAtendido(false);
        $cola->setActivo(true);
        $cola->setFechaTurno(new \DateTime("now"));

        $fecha = date("Y/m/d");
        $repository = $this->em->getRepository('AdminBundle:ColaTurno','p')->createQueryBuilder('p');
        $repository->where('p.fechaTurno between  :fecha_turno_desde  and :fecha_turno_hasta')->setParameter('fecha_turno_desde', $fecha.' 00:00:00')->setParameter('fecha_turno_hasta', $fecha.' 23:59:59');
        $repository->andWhere('p.sede = :sedeId')->setParameter('sedeId', $cola->getSede()->getId());
        if($prioritario){
            $repository->andWhere('p.prioritario = false');
        }else{
            $repository->andWhere('p.prioritario = false');
        }
        $cantidad = count($repository->getQuery()->getResult());
        if($cantidad > 0) {
            $numero = $cantidad % 100;
            $cantidad = intdiv($cantidad, 100);
            if ($resto = 99) {
                $cantidad = $cantidad + 1;
                $numero = 1;
            } else {
                $numero = $numero + 1;
            }
        }else{
            $cantidad = 0;
            $numero= 1;
        }
        $cola->setLetra($this->obtenerLetra($cantidad,$prioritario));
        $cola->setNumero($numero);

        $this->em->persist($cola);
        $this->em->flush();

        return true;
    }

    private function obtenerLetra($cantidad,$reservado){
        $letra = '';
        if($reservado){
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
        }else{
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

    public function guardarTurno($turno){
        //Controlo Disponibilidad
        if($this->checkDatos($turno)) {
            if($this->disponibilidad->controlaDisponibilidad($turno->getFechaTurno(),$turno->getHoraTurno(),$turno->getSede()->getId())){
                $this->em->getConnection()->beginTransaction(); // suspend auto-commit
                try {
                    $turno->setViaMostrador(false);
                    $turno->setNumero( $this->get('manager.turnos')->obtenerProximoTurnoSede($turno->getSede()->getId()) );
                    $this->em = $this->getDoctrine()->getManager();
                    $this->em->persist($turno);
                    $this->em->flush();

                    $this->em->getConnection()->commit();
                } catch (Exception $e) {
                    $this->em->getConnection()->rollBack();
                    throw $e;
                }
            }
        }else{
            throw $this->createNotFoundException('No se encuentra con la disponiblidad');
        }
        //OK
        return $turno;
    }

    private function checkDatos($turno){
        //Controlo la sede
        $sede = $this->em->getRepository('AdminBundle:Sede')->findBy('id',$turno->getSede()->getId());
        if(is_null($sede)){
            throw $this->createNotFoundException('No se encuentra la sede');
        }
        $tipoTramite = $this->em->getRepository('AdminBundle:TipoTramite')->findBy('id',$turno->getTipoTramite()->getId());
        if(is_null($tipoTramite)){
            throw $this->createNotFoundException('No se encuentra el tipo de tramite');
        }

    }

}