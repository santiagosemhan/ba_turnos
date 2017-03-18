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
 *         @ORM\Index(name="Index2", columns={"tipoTramiteId","fechaTurno","horaTurno","sedeId","fechaCancelado"})
 *     })
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TurnoRepository")
 */
class Turno extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $nombreApellido;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cuit;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mail1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mail2;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaTurno;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $horaTurno;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $viaMostrador;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaConfirmacion;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaCancelado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="turno")
     * @ORM\JoinColumn(name="usuario_confirmacion", referencedColumnName="id")
     */
    private $user;
}