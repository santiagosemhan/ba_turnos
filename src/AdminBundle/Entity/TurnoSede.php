<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="turno_sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TurnoSedeRepository")
 */
class TurnoSede extends BaseClass
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
     * @ORM\Column(name="cantidad_sin_turnos",type="integer", nullable=true)
     */
    private $cantidadSinTurnos;

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
     * @ORM\OneToMany(targetEntity="TurnoTipoTramite", mappedBy="turnoSede",cascade={"persist","remove" })
     */
    private $turnoTipoTramite;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="turnoSede")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\UsuarioTurnoSede", mappedBy="turnoSede",cascade={"persist","remove" })
     */
    private $usuarioTurnoSede;

    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="turnoSede",cascade={"persist","remove" })
     */
    private $turno;


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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * Set cantidadSinTurnos
     *
     * @param integer $cantidadSinTurnos
     *
     * @return TurnoSede
     */
    public function setCantidadSinTurnos($cantidadSinTurnos)
    {
        $this->cantidadSinTurnos = $cantidadSinTurnos;

        return $this;
    }

    /**
     * Get cantidadSinTurnos
     *
     * @return integer
     */
    public function getCantidadSinTurnos()
    {
        return $this->cantidadSinTurnos;
    }

    /**
     * Set cantidadFrecuencia
     *
     * @param integer $cantidadFrecuencia
     *
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }


    /**
     * Set sede
     *
     * @param \AdminBundle\Entity\Sede $sede
     *
     * @return TurnoSede
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
     * @return TurnoSede
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
     * @return TurnoSede
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
            //$string = $string.' Vig.Desde:-';
        }
        if($this->vigenciaHasta){
            $string = $string.' Vig.Hasta:'.$this->vigenciaHasta->format('d/m/Y');
        }else{
            //$string = $string.' Vig.Hasta:-';
        }

        return $string;
    }

    public function __toString()
    {
        return $this->sede->getSede().' | Dias:'.$this->getDiasAtiende().' | '.$this->getHorasAtiende().' | '.$this->getVigenciasTurno();
    }




    /**
     * Set usuarioTurnoSede
     *
     * @param \AdminBundle\Entity\UsuarioTurnoSede $usuarioTurnoSede
     *
     * @return TurnoSede
     */
    public function setUsuarioTurnoSede(\AdminBundle\Entity\UsuarioTurnoSede $usuarioTurnoSede = null)
    {
        $this->usuarioTurnoSede = $usuarioTurnoSede;

        return $this;
    }

    /**
     * Get usuarioTurnoSede
     *
     * @return \AdminBundle\Entity\UsuarioTurnoSede
     */
    public function getUsuarioTurnoSede()
    {
        return $this->usuarioTurnoSede;
    }

    /**
     * Add usuarioTurnoSede
     *
     * @param \AdminBundle\Entity\UsuarioTurnoSede $usuarioTurnoSede
     *
     * @return TurnoSede
     */
    public function addUsuarioTurnoSede(\AdminBundle\Entity\UsuarioTurnoSede $usuarioTurnoSede)
    {
        $this->usuarioTurnoSede[] = $usuarioTurnoSede;

        return $this;
    }

    /**
     * Remove usuarioTurnoSede
     *
     * @param \AdminBundle\Entity\UsuarioTurnoSede $usuarioTurnoSede
     */
    public function removeUsuarioTurnoSede(\AdminBundle\Entity\UsuarioTurnoSede $usuarioTurnoSede)
    {
        $this->usuarioTurnoSede->removeElement($usuarioTurnoSede);
    }

    /**
     * Add turnoTipoTramite
     *
     * @param \AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite
     *
     * @return TurnoSede
     */
    public function addTurnoTipoTramite(\AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite)
    {
        $this->turnoTipoTramite[] = $turnoTipoTramite;

        return $this;
    }

    /**
     * Remove turnoTipoTramite
     *
     * @param \AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite
     */
    public function removeTurnoTipoTramite(\AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite)
    {
        $this->turnoTipoTramite->removeElement($turnoTipoTramite);
    }

    public function hasTurnoTipoTramite(\AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite){
        foreach ($this->turnoTipoTramite as $turnoTipo){
            if($turnoTipo->getId == $turnoTipoTramite->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * Get turnoTipoTramite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnoTipoTramite()
    {
        return $this->turnoTipoTramite;
    }

    /**
     * Add turno
     *
     * @param \AdminBundle\Entity\Turno $turno
     *
     * @return TurnoSede
     */
    public function addTurno(\AdminBundle\Entity\Turno $turno)
    {
        $this->turno[] = $turno;

        return $this;
    }

    /**
     * Remove turno
     *
     * @param \AdminBundle\Entity\Turno $turno
     */
    public function removeTurno(\AdminBundle\Entity\Turno $turno)
    {
        $this->turno->removeElement($turno);
    }

    /**
     * Get turno
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurno()
    {
        return $this->turno;
    }
}
