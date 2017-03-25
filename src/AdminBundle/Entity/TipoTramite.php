<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="tipo_tramite")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TipoTramiteRepository")
 */
class TipoTramite extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="descripcion",type="string", length=120, nullable=false)
     */
    private $descripcion;

    /**
     * @ORM\Column(name="texto",type="string", length=2000, nullable=true)
     */
    private $texto;

    /**
     * @var bool
     *
     * @ORM\Column(name="sin_turno", type="boolean")
     */
    protected $sinTurno = false;

    /**
     * @ORM\Column(name="documento1",type="string", length=120, nullable=true)
     */
    private $documento1;

    /**
     * @ORM\Column(name="documento2",type="string", length=120, nullable=true)
     */
    private $documento2;

    /**
     * @ORM\Column(name="documento3",type="string", length=120, nullable=true)
     */
    private $documento3;

    /**
     * @ORM\Column(name="documento4",type="string", length=120, nullable=true)
     */
    private $documento4;

    /**
     * @ORM\Column(name="documento5",type="string", length=120, nullable=true)
     */
    private $documento5;

    /**
     * @ORM\Column(name="documento6",type="string", length=120, nullable=true)
     */
    private $documento6;

    /**
     * @ORM\OneToOne(targetEntity="SedeTipoTramite", mappedBy="tipoTramite")
     */
    private $sedeTipoTramite;

    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="tipoTramite")
     */
    private $turno;

    /**
     * @ORM\OneToMany(targetEntity="TurnoTramite", mappedBy="tipoTramite")
     */
    private $turnoTramite;

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
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return mixed
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * @param mixed $texto
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
    }

    /**
     * @return mixed
     */
    public function getSinTurno()
    {
        return $this->sinTurno;
    }

    /**
     * @param mixed $sinTurno
     */
    public function setSinTurno($sinTurno)
    {
        $this->sinTurno = $sinTurno;
    }

    /**
     * @return mixed
     */
    public function getDocumento1()
    {
        return $this->documento1;
    }

    /**
     * @param mixed $documento1
     */
    public function setDocumento1($documento1)
    {
        $this->documento1 = $documento1;
    }

    /**
     * @return mixed
     */
    public function getDocumento2()
    {
        return $this->documento2;
    }

    /**
     * @param mixed $documento2
     */
    public function setDocumento2($documento2)
    {
        $this->documento2 = $documento2;
    }

    /**
     * @return mixed
     */
    public function getDocumento3()
    {
        return $this->documento3;
    }

    /**
     * @param mixed $documento3
     */
    public function setDocumento3($documento3)
    {
        $this->documento3 = $documento3;
    }

    /**
     * @return mixed
     */
    public function getDocumento4()
    {
        return $this->documento4;
    }

    /**
     * @param mixed $documento4
     */
    public function setDocumento4($documento4)
    {
        $this->documento4 = $documento4;
    }

    /**
     * @return mixed
     */
    public function getDocumento5()
    {
        return $this->documento5;
    }

    /**
     * @param mixed $documento5
     */
    public function setDocumento5($documento5)
    {
        $this->documento5 = $documento5;
    }

    /**
     * @return mixed
     */
    public function getDocumento6()
    {
        return $this->documento6;
    }

    /**
     * @param mixed $documento6
     */
    public function setDocumento6($documento6)
    {
        $this->documento6 = $documento6;
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
    public function getTurnoTramite()
    {
        return $this->turnoTramite;
    }

    /**
     * @param mixed $turnoTramite
     */
    public function setTurnoTramite($turnoTramite)
    {
        $this->turnoTramite = $turnoTramite;
    }


}