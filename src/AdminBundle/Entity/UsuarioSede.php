<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="usuario_sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\UsuarioSedeRepository")
 */
class UsuarioSede extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="usuarioSede")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;

    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="usuarioSede",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     *
     */
    private $usuario;


    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return UsuarioSede
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
     * @return UsuarioSede
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
     * @return UsuarioSede
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
     * Set userio
     *
     * @param \UserBundle\Entity\User $userio
     *
     * @return UsuarioSede
     */
    public function setUserio(\UserBundle\Entity\User $userio)
    {
        $this->userio = $userio;

        return $this;
    }

    /**
     * Get userio
     *
     * @return \UserBundle\Entity\User
     */
    public function getUserio()
    {
        return $this->userio;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return UsuarioSede
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
     * @return UsuarioSede
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    public function __toString()
    {
        return $this->getSede()->getSede();
    }

    /**
     * Set usuario
     *
     * @param \UserBundle\Entity\User $usuario
     *
     * @return UsuarioSede
     */
    public function setUsuario(\UserBundle\Entity\User $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \UserBundle\Entity\User
     */
    public function getUsuario()
    {
        return $this->usuario;
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
}
