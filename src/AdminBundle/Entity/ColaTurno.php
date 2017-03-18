<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use UserBundle\Entity\User;

/**
 * @ORM\Table(
 *     name="cola_turno",
 *     indexes={@ORM\Index(name="Index1", columns={"fechaTurno","atendido","sedeId"})}
 *     )
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ColaTurnoRepository")
 */
class ColaTurno extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $prioritario;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaTurno;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $letra;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $llamado;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaLlamado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $atendido;

    /**
     * @ORM\Column(type="date", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="colaTurno")
     * @ORM\JoinColumn(name="usuario_atendido", referencedColumnName="id")
     */
    private $user;
}