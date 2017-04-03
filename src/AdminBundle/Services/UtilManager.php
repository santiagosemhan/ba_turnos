<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 02/04/2017
 * Time: 9:52
 */


namespace AdminBundle\Services;

use AdminBundle\Entity\UsuarioSede;
use Doctrine\ORM\EntityManager;


class UtilManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

     /*
     * get Hora
     *
     * @param String $horaString (Formato H:i A)
     *
     * @return DateTime (Formato H:i:s)
     */
    public function getHoraDateTime($horaString){
        var_dump($horaString);
        $hora = (substr($horaString,0,2));
        $min = (substr($horaString,3,2));
        if(substr($horaString,6,2) == 'PM'){
            if($hora!=12){
                $hora = $hora +12;
            }
        }
        return( new \DateTime($hora.':'.$min.':00'));
    }

    /*
    * get Fecha

    * @param String $fechaString (Formato d/m/Y)
    * @param String $hora (Formato H:i:s) Opcional
    *
    * @return DateTime
    */
    public function getFechaDateTime($fechaString,$hora=null){
        if(is_null($hora)) {
            return \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s", (mktime(0, 0, 0, intval(substr($fechaString, 3, 2)), intval(substr($fechaString, 0, 2)), intval(substr($fechaString, 6, 4))))));
        }else{
            return \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s", (mktime(intval(substr($hora, 0, 2)), intval(substr($hora, 3, 2)), intval(substr($hora, 6, 2)), intval(substr($fechaString, 3, 2)), intval(substr($fechaString, 0, 2)), intval(substr($fechaString, 6, 4))))));
        }
    }

    /*
    * get Ultima Fecha del Mes
    *
    * @param integer $mes
    * @param integer $anio
    * @param String $hora (Formato H:i:s) Opcional
    *
    * @return DateTime
    */
    public function getUltimaFechaMesDateTime($mes,$anio,$hora=null){
        $dia = date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));
        $dia =sprintf("%02d", $dia);
        if(is_null($hora)) {
            return \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s", (mktime(0, 0, 0, intval($mes), intval($dia), intval($anio)))));
        }else {
            return \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s", (mktime(intval(substr($hora, 0, 2)), intval(substr($hora, 3, 2)), intval(substr($hora, 6, 2)), intval($mes), intval($dia), intval($anio)))));
        }
    }

    /*
    * get Cantidad Por Tipo Dias
    *
    * @param integer $mes
    * @param integer $anio
    *
    * @return array
    */
    public function getCantidadPorTipoDias($mes,$anio){
        $ultimoDia=$this->getUltimaFechaMesDateTime($mes,$anio,'23:59:59');
        $dia = 1;
        $ultimoDiaMes = intval($ultimoDia->format('d'));
        $cantidadDias =array();
        while($dia <= $ultimoDiaMes){
            $date = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s", (mktime(0, 0, 0, intval($mes), intval($dia), intval($anio)))));
            if(isset($cantidadDias[$date->format('N')])){
                $cantidadDias[$date->format('N')] = $cantidadDias[$date->format('N')] +1;
            }else{
                $cantidadDias[$date->format('N')] = 1;
            }
            $dia++;
        }
        return $cantidadDias;
    }

    public function getDiaSemana($dia,$mes,$anio){
        $date = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s", (mktime(0, 0, 0, intval($mes), intval($dia), intval($anio)))));
        return $date->format('N');
    }


}