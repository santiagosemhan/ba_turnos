<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 29/03/2017
 * Time: 18:47
 */



namespace AdminBundle\Services;

use AdminBundle\Entity\UsuarioSede;
use Doctrine\ORM\EntityManager;


class UsuarioManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /*
     * get Sede
     *
     * @return UsuarioSede
     */
    public function getSede($userId){
        $repository = $this->em->getRepository('AdminBundle:UsuarioSede');
        $usuarioSede = $repository->findOneByUsuario($userId);
        if($usuarioSede) {
            return $usuarioSede->getSede();
        }else{
            return null;
        }
    }

}