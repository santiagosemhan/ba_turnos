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
        $qb = $this->createQueryBuilder('tt')
        ->where('tt.sinTurno = :sin_turno')
        ->setParameter("sin_turno", $sinTurno)
        ->getQuery();

        return $hidrate_array ? $qb->getArrayResult() : $qb->getResult();
    }
}
