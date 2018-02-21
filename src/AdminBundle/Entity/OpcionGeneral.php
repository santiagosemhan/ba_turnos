<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 17/04/2017
 * Time: 19:36
 */

namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="opcion_general")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\OpcionGeneralRepository")
 */
class OpcionGeneral extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\Column(name="descripcion",type="string", length=250, nullable=true)
    */
    private $descripcion;

    /**
     * @ORM\Column(name="descripcion_larga",type="string", length=5500, nullable=true)
     */
    private $descripcionLarga;

    /**
     * @ORM\OneToMany(targetEntity="TipoTramite", mappedBy="opcionGeneral")
     */
    private $tiposTramites;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tipoTramite = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getDescripcion();
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
     * @return OpcionGeneral
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
     * Set descripcionLarga
     *
     * @param string $descripcionLarga
     *
     * @return OpcionGeneral
     */
    public function setDescripcionLarga($descripcionLarga)
    {
        $this->descripcionLarga = $descripcionLarga;

        return $this;
    }

    /**
     * Get descripcionLarga
     *
     * @return string
     */
    public function getDescripcionLarga()
    {
        return $this->descripcionLarga;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return OpcionGeneral
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
     * @return OpcionGeneral
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Add tipoTramite
     *
     * @param \AdminBundle\Entity\TipoTramite $tipoTramite
     *
     * @return OpcionGeneral
     */
    public function addTipoTramite(\AdminBundle\Entity\TipoTramite $tipoTramite)
    {
        $this->tiposTramites[] = $tipoTramite;

        return $this;
    }

    /**
     * Remove tipoTramite
     *
     * @param \AdminBundle\Entity\TipoTramite $tipoTramite
     */
    public function removeTipoTramite(\AdminBundle\Entity\TipoTramite $tipoTramite)
    {
        $this->tiposTramites->removeElement($tipoTramite);
    }

    /**
     * Get tipoTramite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTiposTramites()
    {
        return $this->tiposTramites;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return OpcionGeneral
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
     * @return OpcionGeneral
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}