<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="cancelacion_masiva")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\CancelacionMasivaRepository")
 */
class CancelacionMasiva extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="motivo",type="string", length=2000, nullable=false)
     */
    private $motivo;

    /**
     * @ORM\Column(name="fecha",type="date", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="cancelacionMasiva")
     */
    private $turno;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="cancelacionMasiva")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->turno = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set motivo
     *
     * @param string $motivo
     *
     * @return CancelacionMasiva
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return CancelacionMasiva
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return CancelacionMasiva
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
     * @return CancelacionMasiva
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Add turno
     *
     * @param \AdminBundle\Entity\Turno $turno
     *
     * @return CancelacionMasiva
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

    /**
     * Set sede
     *
     * @param \AdminBundle\Entity\Sede $sede
     *
     * @return CancelacionMasiva
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
     * @return CancelacionMasiva
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
     * @return CancelacionMasiva
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
