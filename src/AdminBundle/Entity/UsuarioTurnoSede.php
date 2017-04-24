<?php
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 18/04/2017
 * Time: 17:43
 */

namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="usuario_turno_sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\UsuarioTurnoSedeRepository")
 */
class UsuarioTurnoSede extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="usuarioTipoTramite",cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     *
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="TurnoSede", inversedBy="usuarioTurnoSede",cascade={"persist"})
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
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return UsuarioTurnoSede
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
     * @return UsuarioTurnoSede
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set usuario
     *
     * @param \UserBundle\Entity\User $usuario
     *
     * @return UsuarioTurnoSede
     */
    public function setUsuario(\UserBundle\Entity\User $usuario = null)
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
     * Set turnoSede
     *
     * @param \AdminBundle\Entity\TurnoSede $turnoSede
     *
     * @return UsuarioTurnoSede
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

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return UsuarioTurnoSede
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
     * @return UsuarioTurnoSede
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
