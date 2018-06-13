<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 23/04/2017
 * Time: 16:22
 */

namespace AdminBundle\Repository;


class OpcionGeneralRepository  extends \Doctrine\ORM\EntityRepository
{

    public function getOpcionesGenerales($hidrate_array = false)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.activo = true')
            ->getQuery();

        return $hidrate_array ? $qb->getArrayResult() : $qb->getResult();
    }

    public function getOpcionesGeneralesConTramite($hidrate_array = false)
    {
        $qb = $this->createQueryBuilder('o')
            ->innerJoin('AdminBundle:TipoTramite','t','WITH','o.id = t.opcionGeneral')
            ->where('o.activo = true')
            ->getQuery();

        return $hidrate_array ? $qb->getArrayResult() : $qb->getResult();
    }

    public function getOpcionesGeneralesConTramiteSoloWeb($hidrate_array = false)
    {
        $qb = $this->createQueryBuilder('o')
            ->innerJoin('AdminBundle:TipoTramite','t','WITH','o.id = t.opcionGeneral')

            ->innerJoin('AdminBundle:TurnoTipoTramite', 'tt', 'WITH', 'tt.tipoTramite = t.id')
            ->innerJoin('AdminBundle:TurnoSede', 'ts', 'WITH', 'ts.id = tt.turnoSede')

            ->where('o.activo = true')
            ->andWhere('t.activo = true')
            ->andWhere('ts.soloPresencial != true')
            ->getQuery();

        return $hidrate_array ? $qb->getArrayResult() : $qb->getResult();
    }

}