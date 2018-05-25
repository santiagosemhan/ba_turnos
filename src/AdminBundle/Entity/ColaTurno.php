<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use UserBundle\Entity\User;

/**
 * @ORM\Table(
 *     name="cola_turno",
 *     indexes={@ORM\Index(name="Index1", columns={"fecha_turno","atendido","sede_id"})}
 *     )
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ColaTurnoRepository")
 */
class ColaTurno extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="prioritario",type="boolean", nullable=true)
     */
    private $prioritario;

    /**
     * @ORM\Column(name="fecha_turno",type="datetime", nullable=true)
     */
    private $fechaTurno;

    /**
     * @ORM\Column(name="letra",type="string", length=2, nullable=true)
     */
    private $letra;

    /**
     * @ORM\Column(name="numero",type="integer", length=2, nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(name="llamado",type="boolean", nullable=true)
     */
    private $llamado;

    /**
     * @ORM\Column(name="fecha_llamado",type="datetime", nullable=true)
     */
    private $fechaLlamado;

    /**
     * @ORM\Column(name="atendido",type="boolean", nullable=true)
     */
    private $atendido;

    /**
     * @ORM\Column(name="fecha_atendido",type="datetime", nullable=true)
     */
    private $fechaAtendido;

    /**
     * @ORM\ManyToOne(targetEntity="Turno", inversedBy="colaTurno")
     * @ORM\JoinColumn(name="turno_id", referencedColumnName="id")
     */
    private $turno;

    /**
     * @ORM\ManyToOne(targetEntity="Box", inversedBy="colaTurno")
     * @ORM\JoinColumn(name="box_id", referencedColumnName="id")
     */
    private $box;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="colaTurno")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="colaTurnoAtendio")
     * @ORM\JoinColumn(name="usuario_atendido", referencedColumnName="id")
     */
    private $usuarioAtendido;

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
     * Set prioritario
     *
     * @param boolean $prioritario
     *
     * @return ColaTurno
     */
    public function setPrioritario($prioritario)
    {
        $this->prioritario = $prioritario;

        return $this;
    }

    /**
     * Get prioritario
     *
     * @return boolean
     */
    public function getPrioritario()
    {
        return $this->prioritario;
    }

    /**
     * Set fechaTurno
     *
     * @param \DateTime $fechaTurno
     *
     * @return ColaTurno
     */
    public function setFechaTurno($fechaTurno)
    {
        $this->fechaTurno = $fechaTurno;

        return $this;
    }

    /**
     * Get fechaTurno
     *
     * @return \DateTime
     */
    public function getFechaTurno()
    {
        return $this->fechaTurno;
    }

    /**
     * Set letra
     *
     * @param string $letra
     *
     * @return ColaTurno
     */
    public function setLetra($letra)
    {
        $this->letra = $letra;

        return $this;
    }

    /**
     * Get letra
     *
     * @return string
     */
    public function getLetra()
    {
        return $this->letra;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return ColaTurno
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set llamado
     *
     * @param boolean $llamado
     *
     * @return ColaTurno
     */
    public function setLlamado($llamado)
    {
        $this->llamado = $llamado;

        return $this;
    }

    /**
     * Get llamado
     *
     * @return boolean
     */
    public function getLlamado()
    {
        return $this->llamado;
    }

    /**
     * Set fechaLlamado
     *
     * @param \DateTime $fechaLlamado
     *
     * @return ColaTurno
     */
    public function setFechaLlamado($fechaLlamado)
    {
        $this->fechaLlamado = $fechaLlamado;

        return $this;
    }

    /**
     * Get fechaLlamado
     *
     * @return \DateTime
     */
    public function getFechaLlamado()
    {
        return $this->fechaLlamado;
    }

    /**
     * Set atendido
     *
     * @param boolean $atendido
     *
     * @return ColaTurno
     */
    public function setAtendido($atendido)
    {
        $this->atendido = $atendido;

        return $this;
    }

    /**
     * Get atendido
     *
     * @return boolean
     */
    public function getAtendido()
    {
        return $this->atendido;
    }

    /**
     * Set fechaAtendido
     *
     * @param \DateTime $fechaAtendido
     *
     * @return ColaTurno
     */
    public function setFechaAtendido($fechaAtendido)
    {
        $this->fechaAtendido = $fechaAtendido;

        return $this;
    }

    /**
     * Get fechaAtendido
     *
     * @return \DateTime
     */
    public function getFechaAtendido()
    {
        return $this->fechaAtendido;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return ColaTurno
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
     * @return ColaTurno
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set turno
     *
     * @param \AdminBundle\Entity\Turno $turno
     *
     * @return ColaTurno
     */
    public function setTurno(\AdminBundle\Entity\Turno $turno = null)
    {
        $this->turno = $turno;

        return $this;
    }

    /**
     * Get turno
     *
     * @return \AdminBundle\Entity\Turno
     */
    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * Set box
     *
     * @param \AdminBundle\Entity\Box $box
     *
     * @return ColaTurno
     */
    public function setBox(\AdminBundle\Entity\Box $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return \AdminBundle\Entity\Box
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * Set sede
     *
     * @param \AdminBundle\Entity\Sede $sede
     *
     * @return ColaTurno
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
     * Set usuarioAtendido
     *
     * @param \UserBundle\Entity\User $userioAtendido
     *
     * @return ColaTurno
     */
    public function setUsuarioAtendido(\UserBundle\Entity\User $usuarioAtendido = null)
    {
        $this->usuarioAtendido = $usuarioAtendido;

        return $this;
    }

    /**
     * Get usuarioAtendido
     *
     * @return \UserBundle\Entity\User
     */
    public function getUsuarioAtendido()
    {
        return $this->usuarioAtendido;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return ColaTurno
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
     * @return ColaTurno
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
