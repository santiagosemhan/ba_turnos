<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SedeRepository")
 */
class Sede extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="sede",type="string", length=2000, nullable=true)
     */
    private $sede;

    /**
     * @ORM\Column(name="direccion",nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(name="letra",type="string", length=2, nullable=true)
     */
    private $letra;

    /**
     * @ORM\Column(name="ultimo_turno",type="integer", length=10, nullable=true)
     */
    private $ultimoTurno;

    /**
     * @ORM\OneToOne(targetEntity="SedeTipoTramite", mappedBy="sede")
     */
    private $sedeTipoTramite;

    /**
     * @ORM\OneToMany(targetEntity="Feriado", mappedBy="sede")
     */
    private $feriado;

    /**
     * @ORM\OneToMany(targetEntity="Box", mappedBy="sede")
     */
    private $box;

    /**
     * @ORM\OneToMany(targetEntity="CancelacionMasiva", mappedBy="sede")
     */
    private $cancelacionMasiva;

    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="sede")
     */
    private $turno;

    /**
     * @ORM\OneToMany(targetEntity="Login", mappedBy="sede")
     */
    private $login;

    /**
     * @ORM\OneToMany(targetEntity="UsuarioSede", mappedBy="sede")
     */
    private $usuarioSede;

    /**
     * @ORM\OneToMany(targetEntity="TurnosSede", mappedBy="sede")
     */
    private $turnosSede;

    /**
     * @ORM\OneToMany(targetEntity="ColaTurno", mappedBy="sede")
     */
    private $colaTurno;



    /**
     * @return bool
     */
    public function isActivo()
    {
        return $this->activo;
    }

    /**
     * @param bool $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * @param \DateTime $fechaCreacion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
    }

    /**
     * @return \DateTime
     */
    public function getFechaActualizacion()
    {
        return $this->fechaActualizacion;
    }

    /**
     * @param \DateTime $fechaActualizacion
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;
    }

    /**
     * @return string
     */
    public function getCreadoPor()
    {
        return $this->creadoPor;
    }

    /**
     * @param string $creadoPor
     */
    public function setCreadoPor($creadoPor)
    {
        $this->creadoPor = $creadoPor;
    }

    /**
     * @return string
     */
    public function getActualizadoPor()
    {
        return $this->actualizadoPor;
    }

    /**
     * @param string $actualizadoPor
     */
    public function setActualizadoPor($actualizadoPor)
    {
        $this->actualizadoPor = $actualizadoPor;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSede()
    {
        return $this->sede;
    }

    /**
     * @param mixed $sede
     */
    public function setSede($sede)
    {
        $this->sede = $sede;
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * @return mixed
     */
    public function getLetra()
    {
        return $this->letra;
    }

    /**
     * @param mixed $letra
     */
    public function setLetra($letra)
    {
        $this->letra = $letra;
    }

    /**
     * @return mixed
     */
    public function getUltimoTurno()
    {
        return $this->ultimoTurno;
    }

    /**
     * @param mixed $ultimoTurno
     */
    public function setUltimoTurno($ultimoTurno)
    {
        $this->ultimoTurno = $ultimoTurno;
    }

    /**
     * @return mixed
     */
    public function getSedeTipoTramite()
    {
        return $this->sedeTipoTramite;
    }

    /**
     * @param mixed $sedeTipoTramite
     */
    public function setSedeTipoTramite($sedeTipoTramite)
    {
        $this->sedeTipoTramite = $sedeTipoTramite;
    }

    /**
     * @return mixed
     */
    public function getFeriado()
    {
        return $this->feriado;
    }

    /**
     * @param mixed $feriado
     */
    public function setFeriado($feriado)
    {
        $this->feriado = $feriado;
    }

    /**
     * @return mixed
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * @param mixed $box
     */
    public function setBox($box)
    {
        $this->box = $box;
    }

    /**
     * @return mixed
     */
    public function getCancelacionMasiva()
    {
        return $this->cancelacionMasiva;
    }

    /**
     * @param mixed $cancelacionMasiva
     */
    public function setCancelacionMasiva($cancelacionMasiva)
    {
        $this->cancelacionMasiva = $cancelacionMasiva;
    }

    /**
     * @return mixed
     */
    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * @param mixed $turno
     */
    public function setTurno($turno)
    {
        $this->turno = $turno;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getUsuarioSede()
    {
        return $this->usuarioSede;
    }

    /**
     * @param mixed $usuarioSede
     */
    public function setUsuarioSede($usuarioSede)
    {
        $this->usuarioSede = $usuarioSede;
    }

    /**
     * @return mixed
     */
    public function getTurnosSede()
    {
        return $this->turnosSede;
    }

    /**
     * @param mixed $turnosSede
     */
    public function setTurnosSede($turnosSede)
    {
        $this->turnosSede = $turnosSede;
    }

    /**
     * @return mixed
     */
    public function getColaTurno()
    {
        return $this->colaTurno;
    }

    /**
     * @param mixed $colaTurno
     */
    public function setColaTurno($colaTurno)
    {
        $this->colaTurno = $colaTurno;
    }

    /**
    * Get __toString
    *
    * @return mixed
    */
    public function __toString()
    {
        return $this->getSede();
    }



}