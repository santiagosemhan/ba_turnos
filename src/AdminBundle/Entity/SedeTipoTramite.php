<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(
 *     name="sede_tipo_tramite"
 * )
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SedeTipoTramiteRepository")
 *
 */
class SedeTipoTramite extends BaseClass
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="sedeTipoTramite")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id",nullable=true)
     */
    private $sede;

    /**
     * @ORM\ManyToOne(targetEntity="TipoTramite", inversedBy="sedeTipoTramite")
     * @ORM\JoinColumn(name="tipo_tramite_id", referencedColumnName="id",nullable=true)
     */
    private $tipoTramite;

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return SedeTipoTramite
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
     * @return SedeTipoTramite
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
     * @return SedeTipoTramite
     */
    public function setSede(\AdminBundle\Entity\Sede $sede)
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
     * Set tipoTramite
     *
     * @param \AdminBundle\Entity\TipoTramite $tipoTramite
     *
     * @return SedeTipoTramite
     */
    public function setTipoTramite(\AdminBundle\Entity\TipoTramite $tipoTramite)
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
     * @return SedeTipoTramite
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
     * @return SedeTipoTramite
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
