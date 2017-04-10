<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="turno_sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TurnosSedeRepository")
 */
class TurnosSede extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="lunes",type="boolean", nullable=true)
     */
    private $lunes;

    /**
     * @ORM\Column(name="martes",type="boolean", nullable=true)
     */
    private $martes;

    /**
     * @ORM\Column(name="miercoles",type="boolean", nullable=true)
     */
    private $miercoles;

    /**
     * @ORM\Column(name="jueves",type="boolean", nullable=true)
     */
    private $jueves;

    /**
     * @ORM\Column(name="viernes",type="boolean", nullable=true)
     */
    private $viernes;

    /**
     * @ORM\Column(name="sabado",type="boolean", nullable=true)
     */
    private $sabado;

    /**
     * @ORM\Column(name="hora_turnos_desde",type="time", nullable=true)
     */
    private $horaTurnosDesde;

    /**
     * @ORM\Column(name="hora_turnos_hasta",type="time", nullable=true)
     */
    private $horaTurnosHasta;

    /**
     * @ORM\Column(name="cantidad_turnos",type="integer", nullable=true)
     */
    private $cantidadTurnos;

    /**
     * @ORM\Column(name="cantidad_frecuencia",type="integer", nullable=true)
     */
    private $cantidadFrecuencia;

    /**
     * @ORM\Column(name="frecuncia_turno_control",type="string", nullable=true)
     */
    private $frecunciaTurnoControl;


    /**
     * @ORM\Column(name="vigencia_desde",type="datetime", nullable=true)
     */
    private $vigenciaDesde;

    /**
     * @ORM\Column(name="vigencia_hasta",type="datetime", nullable=true)
     */
    private $vigenciaHasta;

    /**
     * @ORM\OneToMany(targetEntity="TurnoTramite", mappedBy="turnosSede")
     */
    private $turnoTramite;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="turnosSede")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->turnoTramite = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lunes
     *
     * @param boolean $lunes
     *
     * @return TurnosSede
     */
    public function setLunes($lunes)
    {
        $this->lunes = $lunes;

        return $this;
    }

    /**
     * Get lunes
     *
     * @return boolean
     */
    public function getLunes()
    {
        return $this->lunes;
    }

    /**
     * Set martes
     *
     * @param boolean $martes
     *
     * @return TurnosSede
     */
    public function setMartes($martes)
    {
        $this->martes = $martes;

        return $this;
    }

    /**
     * Get martes
     *
     * @return boolean
     */
    public function getMartes()
    {
        return $this->martes;
    }

    /**
     * Set miercoles
     *
     * @param boolean $miercoles
     *
     * @return TurnosSede
     */
    public function setMiercoles($miercoles)
    {
        $this->miercoles = $miercoles;

        return $this;
    }

    /**
     * Get miercoles
     *
     * @return boolean
     */
    public function getMiercoles()
    {
        return $this->miercoles;
    }

    /**
     * Set jueves
     *
     * @param boolean $jueves
     *
     * @return TurnosSede
     */
    public function setJueves($jueves)
    {
        $this->jueves = $jueves;

        return $this;
    }

    /**
     * Get jueves
     *
     * @return boolean
     */
    public function getJueves()
    {
        return $this->jueves;
    }

    /**
     * Set viernes
     *
     * @param boolean $viernes
     *
     * @return TurnosSede
     */
    public function setViernes($viernes)
    {
        $this->viernes = $viernes;

        return $this;
    }

    /**
     * Get viernes
     *
     * @return boolean
     */
    public function getViernes()
    {
        return $this->viernes;
    }

    /**
     * Set sabado
     *
     * @param boolean $sabado
     *
     * @return TurnosSede
     */
    public function setSabado($sabado)
    {
        $this->sabado = $sabado;

        return $this;
    }

    /**
     * Get sabado
     *
     * @return boolean
     */
    public function getSabado()
    {
        return $this->sabado;
    }

    /**
     * Set horaTurnosDesde
     *
     * @param \DateTime $horaTurnosDesde
     *
     * @return TurnosSede
     */
    public function setHoraTurnosDesde($horaTurnosDesde)
    {
        $this->horaTurnosDesde = $horaTurnosDesde;

        return $this;
    }

    /**
     * Get horaTurnosDesde
     *
     * @return \Time
     */
    public function getHoraTurnosDesde()
    {
        return $this->horaTurnosDesde;

    }


    /**
     * Set horaTurnosHasta
     *
     * @param \DateTime $horaTurnosHasta
     *
     * @return TurnosSede
     */
    public function setHoraTurnosHasta($horaTurnosHasta)
    {
        $this->horaTurnosHasta = $horaTurnosHasta;

        return $this;
    }

    /**
     * Get horaTurnosHasta
     *
     * @return \Time
     */
    public function getHoraTurnosHasta()
    {
        return $this->horaTurnosHasta;
    }

    /**
     * Set cantidadTurnos
     *
     * @param integer $cantidadTurnos
     *
     * @return TurnosSede
     */
    public function setCantidadTurnos($cantidadTurnos)
    {
        $this->cantidadTurnos = $cantidadTurnos;

        return $this;
    }

    /**
     * Get cantidadTurnos
     *
     * @return integer
     */
    public function getCantidadTurnos()
    {
        return $this->cantidadTurnos;
    }

    /**
     * Set cantidadFrecuencia
     *
     * @param integer $cantidadFrecuencia
     *
     * @return TurnosSede
     */
    public function setCantidadFrecuencia($cantidadFrecuencia)
    {
        $this->cantidadFrecuencia = $cantidadFrecuencia;

        return $this;
    }

    /**
     * Get cantidadFrecuencia
     *
     * @return integer
     */
    public function getCantidadFrecuencia()
    {
        return $this->cantidadFrecuencia;
    }

    /**
     * Set frecunciaTurnoControl
     *
     * @param integer $frecunciaTurnoControl
     *
     * @return TurnosSede
     */
    public function setFrecunciaTurnoControl($frecunciaTurnoControl)
    {
        $this->frecunciaTurnoControl = $frecunciaTurnoControl;

        return $this;
    }

    /**
     * Get frecunciaTurnoControl
     *
     * @return integer
     */
    public function getFrecunciaTurnoControl()
    {
        return $this->frecunciaTurnoControl;
    }

    /**
     * Set vigenciaDesde
     *
     * @param \DateTime $vigenciaDesde
     *
     * @return TurnosSede
     */
    public function setVigenciaDesde($vigenciaDesde)
    {
        $this->vigenciaDesde = $vigenciaDesde;

        return $this;
    }

    /**
     * Get vigenciaDesde
     *
     * @return \DateTime
     */
    public function getVigenciaDesde()
    {
        return $this->vigenciaDesde;

    }

    /**
     * Get vigenciaDesde
     *
     * @return \DateTime
     */
    public function getVigenciaDesdeDateTime()
    {
        return $this->vigenciaDesde;

    }

    /**
     * Set vigenciaHasta
     *
     * @param \DateTime $vigenciaHasta
     *
     * @return TurnosSede
     */
    public function setVigenciaHasta($vigenciaHasta)
    {
        $this->vigenciaHasta = $vigenciaHasta;

        return $this;
    }

    /**
     * Get vigenciaHasta
     *
     * @return \DateTime
     */
    public function getVigenciaHasta()
    {
        return $this->vigenciaHasta;

    }

    /**
     * Get vigenciaDesde
     *
     * @return \DateTime
     */
    public function getVigenciaHastaDateTime()
    {
        return $this->vigenciaHasta;

    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return TurnosSede
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Set fechaActualizacion
     *
     * @param \DateTime $fechaActualizacion
     *
     * @return TurnosSede
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Add turnoTramite
     *
     * @param \AdminBundle\Entity\TurnoTramite $turnoTramite
     *
     * @return TurnosSede
     */
    public function addTurnoTramite(\AdminBundle\Entity\TurnoTramite $turnoTramite)
    {
        $this->turnoTramite[] = $turnoTramite;

        return $this;
    }

    /**
     * Remove turnoTramite
     *
     * @param \AdminBundle\Entity\TurnoTramite $turnoTramite
     */
    public function removeTurnoTramite(\AdminBundle\Entity\TurnoTramite $turnoTramite)
    {
        $this->turnoTramite->removeElement($turnoTramite);
    }

    /**
     * Get turnoTramite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnoTramite()
    {
        return $this->turnoTramite;
    }

    /**
     * Set sede
     *
     * @param \AdminBundle\Entity\Sede $sede
     *
     * @return TurnosSede
     */
    public function setSede(\AdminBundle\Entity\Sede $sede = null)
    {
        $this->sede = $sede;

        return $this;
    }

    /**
     * Get sede
     *
     * @return \AdminBundle\Entity\Sede
     */
    public function getSede()
    {
        return $this->sede;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return TurnosSede
     */
    public function setCreadoPor(\UserBundle\Entity\User $creadoPor = null)
    {
        $this->creadoPor = $creadoPor;

        return $this;
    }

    /**
     * Set actualizadoPor
     *
     * @param \UserBundle\Entity\User $actualizadoPor
     *
     * @return TurnosSede
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    public function getDiasAtiende(){
        $string = '';
        if($this->getLunes()){
            $string = 'Lu ';
        }
        if($this->getMartes()){
            $string = $string.'Ma ';
        }
        if($this->getMiercoles()){
            $string = $string.'Mi ';
        }
        if($this->getJueves()){
            $string = $string.'Ju ';
        }
        if($this->getViernes()){
            $string = $string.'Vi ';
        }
        if($this->getSabado()){
            $string = $string.'Sa ';
        }
        return $string;
    }

    public function getHorasAtiende(){
       return 'Hora.Desde:'.$this->horaTurnosDesde->format('H:i A').' Hora.Hasta:'.$this->horaTurnosHasta->format('H:i A');
    }

    public function getVigenciasTurno(){
        $string = "";
        if($this->vigenciaDesde){
            $string = $string.' Vig.Desde:'.$this->vigenciaDesde->format('d/m/Y');
        }else{
            $string = $string.' Vig.Desde:-';
        }
        if($this->vigenciaHasta){
            $string = $string.' Vig.Hasta:'.$this->vigenciaHasta->format('d/m/Y');
        }else{
            $string = $string.' Vig.Hasta:-';
        }

        return $string;
    }

    public function __toString()
    {
        return $this->sede->getSede().' | Dias:'.$this->getDiasAtiende().' | '.$this->getHorasAtiende().' | '.$this->getVigenciasTurno();
    }

}
