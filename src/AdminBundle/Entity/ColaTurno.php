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
     * @ORM\Column(name="fecha_turno",type="date", nullable=true)
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
     * @ORM\Column(name="fecha_llamado",type="date", nullable=true)
     */
    private $fechaLlamado;

    /**
     * @ORM\Column(name="atendido",type="boolean", nullable=true)
     */
    private $atendido;

    /**
     * @ORM\Column(name="fecha_atendido",type="date", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="colaTurno")
     * @ORM\JoinColumn(name="usuario_atendido", referencedColumnName="id")
     */
    private $userioAtendido;
}