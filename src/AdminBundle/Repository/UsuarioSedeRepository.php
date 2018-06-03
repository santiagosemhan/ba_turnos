<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 18/03/2017
 * Time: 18:00
 */

namespace AdminBundle\Repository;

/**
 * UsuarioSedeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsuarioSedeRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllByUsuarioSede($usuarioSede)
    {

        $primero = true;
        $qb = $this->createQueryBuilder('ct');

        if($usuarioSede->getUsuario()){
            $qb = $qb->where('ct.usuario = :usuario')
                ->setParameter("usuario",$usuarioSede->getUsuario()->getId());
            $primero = false;
        }
        if($usuarioSede->getSede()){
            if($primero){
                $qb = $qb->where('ct.sede = :sede')
                    ->setParameter("sede", $usuarioSede->getSede()->getId());
            }else{
                $qb = $qb->andWhere('ct.sede = :sede')
                    ->setParameter("sede", $usuarioSede->getSede()->getId());
            }
        }

        return  $qb->getQuery()->getResult();
    }
}