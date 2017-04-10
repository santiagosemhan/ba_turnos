<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="turno_tramite")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TurnoTramiteRepository")
 */
class TurnoTramite extends BaseClass
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
     * @ORM\Column(name="cantidadSlot",type="integer", nullable=true)
     */
    private $cantidadSlot;

    /**
     * @ORM\ManyToOne(targetEntity="TipoTramite", inversedBy="turnoTramite")
     * @ORM\JoinColumn(name="tipo_tramite_id", referencedColumnName="id")
     */
    private $tipoTramite;

    /**
     * @ORM\ManyToOne(targetEntity="TurnosSede", inversedBy="turnoTramite")
     * @ORM\JoinColumn(name="turno_sede_id", referencedColumnName="id")
     */
    private $turnosSede;

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
     * @param integer $cantidadTurno
     *
     * @return TurnoTramite
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
     * @return TurnoTramite
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
     * @return TurnoTramite
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
     * @return TurnoTramite
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
     * Set turnosSede
     *
     * @param \AdminBundle\Entity\TurnosSede $turnosSede
     *
     * @return TurnoTramite
     */
    public function setTurnosSede(\AdminBundle\Entity\TurnosSede $turnosSede = null)
    {
        $this->turnosSede = $turnosSede;

        return $this;
    }

    /**
     * Get turnosSede
     *
     * @return \AdminBundle\Entity\TurnosSede
     */
    public function getTurnosSede()
    {
        return $this->turnosSede;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return TurnoTramite
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
     * @return TurnoTramite
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
     * @return TurnoTramite
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
}
