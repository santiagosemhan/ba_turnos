<?php

namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="box")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\BoxRepository")
 */
class Box extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="descripcion",type="string", nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity="ColaTurno", mappedBy="box")
     */
    private $colaTurno;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="box",cascade={"persist"})
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;


    public function __toString()
    {
        return $this->descripcion;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->colaTurno = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Box
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Box
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
     * @return Box
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Add colaTurno
     *
     * @param \AdminBundle\Entity\ColaTurno $colaTurno
     *
     * @return Box
     */
    public function addColaTurno(\AdminBundle\Entity\ColaTurno $colaTurno)
    {
        $this->colaTurno[] = $colaTurno;

        return $this;
    }

    /**
     * Remove colaTurno
     *
     * @param \AdminBundle\Entity\ColaTurno $colaTurno
     */
    public function removeColaTurno(\AdminBundle\Entity\ColaTurno $colaTurno)
    {
        $this->colaTurno->removeElement($colaTurno);
    }

    /**
     * Get colaTurno
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColaTurno()
    {
        return $this->colaTurno;
    }

    /**
     * Set sede
     *
     * @param \AdminBundle\Entity\Sede $sede
     *
     * @return Box
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
     * @return Box
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
     * @return Box
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
