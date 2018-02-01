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
    public function getHoraDateTime($horaString)
    {
        $hora = 0;$min=0;
        if (strlen($horaString) == 7) {
            $hora = (substr($horaString, 0, 1));
            $min = (substr($horaString, 2, 2));
            if (substr($horaString, 5, 2) == 'PM') {
                if ($hora != 12) {
                    $hora = $hora + 12;
                }
            }
        } else {
            $hora = (substr($horaString, 0, 2));
            $min = (substr($horaString, 3, 2));
            if (substr($horaString, 6, 2) == 'PM') {
                if ($hora != 12) {
                    $hora = $hora + 12;
                }
            }
        }


        return( new \DateTime($hora.':'.$min.':00'));
    }

    /*
     * get Hora
     *
     * @param DateTime $hora
     *
     * @return String
     */
    public function getHoraString($horaDateTime){
        $string = $horaDateTime->format('h:i A');
        $hora = (substr($string,0,2));
        $min = (substr($string,3,2));
        if(substr($string,6,2) == 'PM'){
            if($hora!=12){
                $hora = $hora +12;
            }
        }
        return( $hora.':'.$min.':00');
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

    public function getFechaDateTimeFromVars($dia,$mes,$anio,$hora=null){
        $diaString='';
        $mesString='';
        switch ($dia) {
            case 1:
                $diaString = '01';
                break;
            case 2:
                $diaString = '02';
                break;
            case 3:
                $diaString = '03';
                break;
            case 4:
                $diaString = '04';
                break;
            case 5:
                $diaString = '05';
                break;
            case 6:
                $diaString = '06';
                break;
            case 7:
                $diaString = '07';
                break;
            case 8:
                $diaString = '08';
                break;
            case 9:
                $diaString = '09';
                break;
        }
        switch ($mes) {
            case 1:
                $mesString = '01';
                break;
            case 2:
                $mesString = '02';
                break;
            case 3:
                $mesString = '03';
                break;
            case 4:
                $mesString = '04';
                break;
            case 5:
                $mesString = '05';
                break;
            case 6:
                $mesString = '06';
                break;
            case 7:
                $mesString = '07';
                break;
            case 8:
                $mesString = '08';
                break;
            case 9:
                $mesString = '09';
                break;
        }
        if($mesString == ''){
            $mesString = $mes;
        }
        if($diaString == ''){
            $diaString = $dia;
        }
        return $this->getFechaDateTime($diaString . '/' . $mesString . '/' . $anio, $hora);
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