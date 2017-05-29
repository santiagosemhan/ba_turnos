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
            ->innerJoin('TipoTramite','t','WITH','o.id = t.opcionGeneral')
            ->where('o.activo = true')
            ->getQuery();

        return $hidrate_array ? $qb->getArrayResult() : $qb->getResult();
    }

}