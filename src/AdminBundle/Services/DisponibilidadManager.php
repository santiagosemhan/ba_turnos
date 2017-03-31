<?php


namespace AdminBundle\Services;

use AdminBundle\Entity\Turnos;
use Doctrine\ORM\EntityManager;

class DisponibilidadManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getDistribucionOperacionesPorEquipo($desde, $hasta)
    {

        $equipos = $persona->getEquiposActivos();

        $distribucionPorEquipoQb = $this->em
            ->getRepository('AppBundle:EstadisticaFinal')
            ->getDistribucionOperacionesPorEquipo($equipos, $desde, $hasta);

        $distribucionPorEquipo = $distribucionPorEquipoQb->getQuery()->getResult();

        $total = 0;

        $data = array();

        if ($distribucionPorEquipo) {

            foreach ($distribucionPorEquipo as $distribucion) {
                $total = $total + $distribucion['cant'];
            }


            foreach ($distribucionPorEquipo as $distribucion) {
                $data[] = array(
                    'name' => $distribucion['acronimo'] . ' ' . $distribucion['nombre'],
                    'y' => ($distribucion['cant'] / $total),
                    'distribucion' => $distribucion['cant']
                );
            }

        }

        return $data;
    }

    public function getDiasDisponibles($mes,$anio,$turnoId){
        $diaHabil = array( 'diasHabilies' => array(1,5,7));
        return $diaHabil;

    }

    public function getHorasDisponibles($dia,$turnoId){
        $horasHabiles = array( 'horasHabiles' => array('07:30','08:00'));
        return $horasHabiles;
    }

}