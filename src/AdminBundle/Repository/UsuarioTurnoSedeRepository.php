<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 22/04/2017
 * Time: 16:09
 */

namespace AdminBundle\Repository;


class UsuarioTurnoSedeRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllByUsuarioTurnoSede($usuarioSede)
    {

        $primero = true;
        $qb = $this->createQueryBuilder('ct');

        if($usuarioSede->getUsuario()){
            $qb = $qb->where('ct.usuario = :usuario')
                ->setParameter("usuario",$usuarioSede->getUsuario()->getId());
            $primero = false;
        }
        if($usuarioSede->getTurnoSede()){
            if($primero){
                $qb = $qb->where('ct.turnoSede = :turnoSede')
                    ->setParameter("turnoSede", $usuarioSede->getTurnoSede()->getId());
            }else{
                $qb = $qb->andWhere('ct.turnoSede = :turnoSede')
                    ->setParameter("turnoSede", $usuarioSede->getTurnoSede()->getId());
            }
        }

        return  $qb->getQuery()->getResult();
    }

}