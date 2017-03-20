<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use UserBundle\Entity\User;

/**
 * @ORM\Table(
 *     name="turno",
 *     indexes={
 *         @ORM\Index(name="Index1", columns={"id"}),
 *         @ORM\Index(name="Index2", columns={"tipo_tramite_id","fecha_turno","hora_turno","sede_id","fecha_cancelado"})
 *     })
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TurnoRepository")
 */
class Turno extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="nombre_apellido",type="string", nullable=true)
     */
    private $nombreApellido;

    /**
     * @ORM\Column(name="telefono",type="string", nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(name="cuit",type="string", nullable=true)
     */
    private $cuit;

    /**
     * @ORM\Column(name="mail1",type="string", nullable=true)
     */
    private $mail1;

    /**
     * @ORM\Column(name="mail2",type="string", nullable=true)
     */
    private $mail2;

    /**
     * @ORM\Column(name="fecha_turno",type="date", nullable=true)
     */
    private $fechaTurno;

    /**
     * @ORM\Column(name="hora_turno",type="string", nullable=true)
     */
    private $horaTurno;


    /**
     * @ORM\Column(name="numero",type="integer", nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(name="via_mostrador",type="boolean", nullable=true)
     */
    private $viaMostrador;

    /**
     * @ORM\Column(name="fecha_confirmacion",type="date", nullable=true)
     */
    private $fechaConfirmacion;

    /**
     * @ORM\Column(name="fecha_cancelado",type="date", nullable=true)
     */
    private $fechaCancelado;

    /**
     * @ORM\Column(name="cancelado_web",type="boolean", nullable=true)
     */
    private $canceladoWeb;

    /**
     * @ORM\OneToMany(targetEntity="Mail", mappedBy="turno")
     */
    private $mail;

    /**
     * @ORM\OneToMany(targetEntity="ColaTurno", mappedBy="turno")
     */
    private $colaTurno;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="turno")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;

    /**
     * @ORM\ManyToOne(targetEntity="TipoTramite", inversedBy="turno")
     * @ORM\JoinColumn(name="tipo_tramite_id", referencedColumnName="id")
     */
    private $tipoTramite;

    /**
     * @ORM\ManyToOne(targetEntity="CancelacionMasiva", inversedBy="turno")
     * @ORM\JoinColumn(name="cancelacion_masiva_id", referencedColumnName="id")
     */
    private $cancelacionMasiva;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="turno")
     * @ORM\JoinColumn(name="usuario_confirmacion", referencedColumnName="id")
     */
    private $userioConfirmacion;
}