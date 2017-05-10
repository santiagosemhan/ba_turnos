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

    public function obtenerExportacion($sedes,$horaDesde,$horaHasta,$usuarios,$fechaDesde,$fechaHasta){

        $repository = $this->em->getRepository('AdminBundle:Login', 'p');
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

        $fechaDesde = date("Y/m/d h:i", mktime($hora, $min, 0, substr($fechaDesde, 3, 2), substr($fechaDesde, 0, 2), substr($fechaDesde, 6, 4)));
        $fechaHasta = date("Y/m/d h:i", mktime($hora2, $min2, 0, substr($fechaHasta, 3, 2), substr($fechaHasta, 0, 2), substr($fechaHasta, 6, 4)));

        $repository->andWhere('p.fechaCreacion between  :fecha_desde  and :fecha_hasta')
                        ->setParameter('fecha_desde', $fechaDesde)
                        ->setParameter('fecha_hasta', $fechaHasta);


        $arraySede = array();
        foreach($sedes as $sede){
            $arraySede[] = $sede->getId();
        }
        $repository->andWhere('p.sede IN (:sedeId)')->setParameter('sedeId', $arraySede);

        $arrayUsuarios = array();
        foreach($usuarios as $usuario){
            $arrayUsuarios[] = $usuario->getId();
        }
        $repository->andWhere('p.usuario IN (:usuarioId)')->setParameter('usuarioId', $arrayUsuarios);

        $repository->orderBy('p.sede', 'ASC');
        $repository->orderBy('p.fechaCreacion', 'ASC');
        $repository->orderBy('p.usuario', 'ASC');

        return  $repository->getQuery()->getResult();
    }

}