<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="turno_tramite")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TurnoTipoTramiteRepository")
 */
class TurnoTipoTramite extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="cantidad_turno",type="integer", nullable=true)
     */
    private $cantidadTurno;

    /**
     * @ORM\Column(name="cantidad_slot",type="integer", nullable=true)
     */
    private $cantidadSlot;

    /**
     * @ORM\ManyToOne(targetEntity="TipoTramite", inversedBy="turnoTipoTramite",cascade={"persist"})
     * @ORM\JoinColumn(name="tipo_tramite_id", referencedColumnName="id")
     */
    private $tipoTramite;

    /**
     * @ORM\ManyToOne(targetEntity="TurnoSede", inversedBy="turnoTipoTramite",cascade={"persist"})
     * @ORM\JoinColumn(name="turno_sede_id", referencedColumnName="id")
     */
    private $turnoSede;


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
     * Set cantidadTurno
     *
     *
     * @param integer $cantidadTurno
     *
     * @return TurnoTipoTramite
     */
    public function setCantidadTurno($cantidadTurno)
    {
        $this->cantidadTurno = $cantidadTurno;

        return $this;
    }

    /**
     * Get cantidadTurno
     *
     * @return integer
     */
    public function getCantidadTurno()
    {
        return $this->cantidadTurno;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return TurnoTipoTramite
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
     * @return TurnoTipoTramite
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set tipoTramite
     *
     * @param \AdminBundle\Entity\TipoTramite $tipoTramite
     *
     * @return TurnoTipoTramite
     */
    public function setTipoTramite(\AdminBundle\Entity\TipoTramite $tipoTramite = null)
    {
        $this->tipoTramite = $tipoTramite;

        return $this;
    }

    /**
     * Get tipoTramite
     *
     * @return \AdminBundle\Entity\TipoTramite
     */
    public function getTipoTramite()
    {
        return $this->tipoTramite;
    }
    

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return TurnoTipoTramite
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
     * @return TurnoTipoTramite
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * Set cantidadSlot
     *
     * @param integer $cantidadSlot
     *
     * @return TurnoTipoTramite
     */
    public function setCantidadSlot($cantidadSlot)
    {
        $this->cantidadSlot = $cantidadSlot;

        return $this;
    }

    /**
     * Get cantidadSlot
     *
     * @return integer
     */
    public function getCantidadSlot()
    {
        return $this->cantidadSlot;
    }

    /**
     * Set usuarioTurnoSede
     *
     * @param \AdminBundle\Entity\TurnoSede $usuarioTurnoSede
     *
     * @return TurnoTipoTramite
     */
    public function setUsuarioTurnoSede(\AdminBundle\Entity\TurnoSede $usuarioTurnoSede = null)
    {
        $this->usuarioTurnoSede = $usuarioTurnoSede;

        return $this;
    }

    /**
     * Get usuarioTurnoSede
     *
     * @return \AdminBundle\Entity\TurnoSede
     */
    public function getUsuarioTurnoSede()
    {
        return $this->usuarioTurnoSede;
    }

    /**
     * Set turnoSede
     *
     * @param \AdminBundle\Entity\TurnoSede $turnoSede
     *
     * @return TurnoTipoTramite
     */
    public function setTurnoSede(\AdminBundle\Entity\TurnoSede $turnoSede = null)
    {
        $this->turnoSede = $turnoSede;

        return $this;
    }

    /**
     * Get turnoSede
     *
     * @return \AdminBundle\Entity\TurnoSede
     */
    public function getTurnoSede()
    {
        return $this->turnoSede;
    }
}
