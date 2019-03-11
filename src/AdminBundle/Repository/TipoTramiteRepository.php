<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 18/03/2017
 * Time: 17:55
 */

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TipoTramiteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TipoTramiteRepository extends EntityRepository
{
    public function getTiposByAgrupador($sinTurno, $hidrate_array = false)
    {
        $qb = $this->createQueryBuilder('tr')
        ->where('tr.sinTurno = :sin_turno AND tr.activo = true')
        ->setParameter("sin_turno", $sinTurno)
        ->getQuery();

        return $hidrate_array ? $qb->getArrayResult() : $qb->getResult();
    }

    public function getTipoTramiteByOpcionesGenerales($id, $hidrate_array = false)
    {
        $qb = $this->createQueryBuilder('tr')
            ->innerJoin('AdminBundle:TurnoTipoTramite', 'tt', 'WITH', 'tt.tipoTramite = tr.id')
            ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'ts.id = tt.turnoSede')
            ->where('tr.opcionGeneral = :opcionGeneral AND tr.activo = true')
            ->setParameter("opcionGeneral", $id)
            ->getQuery();
        return $hidrate_array ? $qb->getArrayResult() : $qb->getResult();
    }

    public function getTipoTramiteByOpcionesGeneralesWeb($id, $hidrate_array = false)
    {
        $qb = $this->createQueryBuilder('tr')
            ->innerJoin('AdminBundle:TurnoTipoTramite', 'tt', 'WITH', 'tt.tipoTramite = tr.id')
            ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'ts.id = tt.turnoSede')
            ->where('tr.opcionGeneral = :opcionGeneral AND tr.activo = true AND ts.soloPresencial != true')
            ->setParameter("opcionGeneral", $id)
            ->getQuery();
        return $hidrate_array ? $qb->getArrayResult() : $qb->getResult();
    }

    public function getTramitesPorSede($sede) {
        $qb = $this->createQueryBuilder('tr')
            ->innerJoin('AdminBundle:TurnoTipoTramite', 'tt', 'WITH', 'tt.tipoTramite = tr.id')
            ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'ts.id = tt.turnoSede')
            ->where('ts.sede = :sede AND tr.activo = true')
            ->andWhere('ts.cantidadSinTurnos > 0')
            ->addOrderBy('tr.opcionGeneral')
            ->addOrderBy('tr.descripcion')
            ->setParameter("sede", $sede);
        return $qb;
    }
}
