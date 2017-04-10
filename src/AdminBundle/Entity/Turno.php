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
     * @ORM\Column(name="fecha_turno",type="datetime", nullable=true)
     */
    private $fechaTurno;

    /**
     * @ORM\Column(name="hora_turno",type="time", nullable=true)
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
     * @ORM\Column(name="fecha_confirmacion",type="datetime", nullable=true)
     */
    private $fechaConfirmacion;

    /**
     * @ORM\Column(name="fecha_cancelado",type="datetime", nullable=true)
     */
    private $fechaCancelado;

    /**
     * @ORM\Column(name="cancelado_web",type="boolean", nullable=true)
     */
    private $canceladoWeb;

    /**
     * @ORM\Column(name="cancelado_mostrador",type="boolean", nullable=true)
     */
    private $canceladoMostrador;

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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="turnoConfirmacion")
     * @ORM\JoinColumn(name="usuario_confirmacion", referencedColumnName="id")
     */
    private $usuarioConfirmacion;

    /**
     * @var $usuarioSede
     *
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Comprobante", mappedBy="turnoId")
     */
    private $comprobante;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mail = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombreApellido
     *
     * @param string $nombreApellido
     *
     * @return Turno
     */
    public function setNombreApellido($nombreApellido)
    {
        $this->nombreApellido = $nombreApellido;

        return $this;
    }

    /**
     * Get nombreApellido
     *
     * @return string
     */
    public function getNombreApellido()
    {
        return $this->nombreApellido;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Turno
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     *
     * @return Turno
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set mail1
     *
     * @param string $mail1
     *
     * @return Turno
     */
    public function setMail1($mail1)
    {
        $this->mail1 = $mail1;

        return $this;
    }

    /**
     * Get mail1
     *
     * @return string
     */
    public function getMail1()
    {
        return $this->mail1;
    }

    /**
     * Set mail2
     *
     * @param string $mail2
     *
     * @return Turno
     */
    public function setMail2($mail2)
    {
        $this->mail2 = $mail2;

        return $this;
    }

    /**
     * Get mail2
     *
     * @return string
     */
    public function getMail2()
    {
        return $this->mail2;
    }

    /**
     * Set fechaTurno
     *
     * @param \DateTime $fechaTurno
     *
     * @return Turno
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
     * Set horaTurno
     *
     * @param string $horaTurno
     *
     * @return Turno
     */
    public function setHoraTurno($horaTurno)
    {
        if (is_string($horaTurno)) {
            $this->horaTurno = new \DateTime($horaTurno);
        } else {
            $this->horaTurno = $horaTurno;
        }

        return $this;
    }

    /**
     * Get horaTurno
     *
     * @return DateTime
     */
    public function getHoraTurno()
    {
        return $this->horaTurno;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Turno
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
     * Set viaMostrador
     *
     * @param boolean $viaMostrador
     *
     * @return Turno
     */
    public function setViaMostrador($viaMostrador)
    {
        $this->viaMostrador = $viaMostrador;

        return $this;
    }

    /**
     * Get viaMostrador
     *
     * @return boolean
     */
    public function getViaMostrador()
    {
        return $this->viaMostrador;
    }

    /**
     * Set fechaConfirmacion
     *
     * @param \DateTime $fechaConfirmacion
     *
     * @return Turno
     */
    public function setFechaConfirmacion($fechaConfirmacion)
    {
        $this->fechaConfirmacion = $fechaConfirmacion;

        return $this;
    }

    /**
     * Get fechaConfirmacion
     *
     * @return \DateTime
     */
    public function getFechaConfirmacion()
    {
        return $this->fechaConfirmacion;
    }

    /**
     * Set fechaCancelado
     *
     * @param \DateTime $fechaCancelado
     *
     * @return Turno
     */
    public function setFechaCancelado($fechaCancelado)
    {
        $this->fechaCancelado = $fechaCancelado;

        return $this;
    }

    /**
     * Get fechaCancelado
     *
     * @return \DateTime
     */
    public function getFechaCancelado()
    {
        return $this->fechaCancelado;
    }

    /**
     * Set canceladoWeb
     *
     * @param boolean $canceladoWeb
     *
     * @return Turno
     */
    public function setCanceladoWeb($canceladoWeb)
    {
        $this->canceladoWeb = $canceladoWeb;

        return $this;
    }

    /**
     * Get canceladoWeb
     *
     * @return boolean
     */
    public function getCanceladoWeb()
    {
        return $this->canceladoWeb;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Turno
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
     * @return Turno
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Add mail
     *
     * @param \AdminBundle\Entity\Mail $mail
     *
     * @return Turno
     */
    public function addMail(\AdminBundle\Entity\Mail $mail)
    {
        $this->mail[] = $mail;

        return $this;
    }

    /**
     * Remove mail
     *
     * @param \AdminBundle\Entity\Mail $mail
     */
    public function removeMail(\AdminBundle\Entity\Mail $mail)
    {
        $this->mail->removeElement($mail);
    }

    /**
     * Get mail
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Add colaTurno
     *
     * @param \AdminBundle\Entity\ColaTurno $colaTurno
     *
     * @return Turno
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
     * @return Turno
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
     * Set tipoTramite
     *
     * @param \AdminBundle\Entity\TipoTramite $tipoTramite
     *
     * @return Turno
     */
    public function setTipoTramite(\AdminBundle\Entity\TipoTramite $tipoTramite = null)
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
     * Set cancelacionMasiva
     *
     * @param \AdminBundle\Entity\CancelacionMasiva $cancelacionMasiva
     *
     * @return Turno
     */
    public function setCancelacionMasiva(\AdminBundle\Entity\CancelacionMasiva $cancelacionMasiva = null)
    {
        $this->cancelacionMasiva = $cancelacionMasiva;

        return $this;
    }

    /**
     * Get cancelacionMasiva
     *
     * @return \AdminBundle\Entity\CancelacionMasiva
     */
    public function getCancelacionMasiva()
    {
        return $this->cancelacionMasiva;
    }

    /**
     * Set usuarioConfirmacion
     *
     * @param \UserBundle\Entity\User $userioConfirmacion
     *
     * @return Turno
     */
    public function setUsuarioConfirmacion(\UserBundle\Entity\User $usuarioConfirmacion = null)
    {
        $this->usuarioConfirmacion = $usuarioConfirmacion;

        return $this;
    }

    /**
     * Get usuarioConfirmacion
     *
     * @return \UserBundle\Entity\User
     */
    public function getUsuarioConfirmacion()
    {
        return $this->usuarioConfirmacion;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return Turno
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
     * @return Turno
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }


    /**
     * Set canceladoMostrador
     *
     * @param boolean $canceladoMostrador
     *
     * @return Turno
     */
    public function setCanceladoMostrador($canceladoMostrador)
    {
        $this->canceladoMostrador = $canceladoMostrador;

        return $this;
    }

    /**
     * Get canceladoMostrador
     *
     * @return boolean
     */
    public function getCanceladoMostrador()
    {
        return $this->canceladoMostrador;
    }

    /**
     * Get Estado
     *
     * @return string
     */
    public function getEstado()
    {
        $estado = '';
        if (is_null($this->fechaConfirmacion)) {
            $estado = 'Sin Corfirmar';
        } else {
            $estado = 'Corfirmado';
        }

        if (count($this->getColaTurno())>0) {
            $estado = 'Atendidos';
        }

        if ($this->canceladoWeb) {
            $estado = 'Cencelado Web';
        }

        if ($this->cancelacionMasiva) {
            $estado = 'Cencelado Mostrador';
        }

        if ($this->cancelacionMasiva) {
            $estado = 'Cencelado Masivo';
        }


        return $estado;
    }

    /**
     * Get Estado
     *
     * @return string
     */
    public function getEstadoInformativo()
    {
        $estado = '';
        if (is_null($this->fechaConfirmacion)) {
            $estado = 'Sin Corfirmar';
        } else {
            if ($this->viaMostrador) {
                $estado = 'Corfirmado Sin Turnos';
            } else {
                $estado = 'Corfirmado Con Turnos';
            }
        }

        if (count($this->getColaTurno())>0) {
            $cola=$this->getColaTurno();
            if ($cola[0]->getAtendido()) {
                if ($this->viaMostrador) {
                    $estado = 'Atendido Sin Turnos';
                } else {
                    $estado = 'Atendido Con Turnos';
                }
            } else {
                if ($this->viaMostrador) {
                    $estado = 'Corfirmado Sin Turnos';
                } else {
                    $estado = 'Corfirmado Con Turnos';
                }
            }
        }

        if ($this->canceladoWeb) {
            $estado = 'Cencelado Web';
        }

        if ($this->cancelacionMasiva) {
            $estado = 'Cencelado Mostrador';
        }

        if ($this->cancelacionMasiva) {
            $estado = 'Cencelado Masivo';
        }


        return $estado;
    }

    /**
     * Get Hora String
     *
     * @return string
     */
    public function getHoraTurnoString()
    {
        return $this->getHoraTurno()->format("H:i");
    }

    /**
     * get Turno Box
     *
     * @return string
     */
    public function getTurnoBox()
    {
        $cola = $this->getColaTurno();
        if (count($cola) > 0) {
            return $cola[0]->getLetra().'-'.sprintf("%02d", $cola[0]->getNumero());
        } else {
            return "El turno no fue confirmado";
        }
    }

    /**
     * get Turno Sede
     *
     * @return string
     */
    public function getTurnoSede()
    {
        return $this->getSede()->getLetra().$this->getNumero();
    }

    /**
     * Set comprobante
     *
     * @param \AdminBundle\Entity\Comprobante $comprobante
     *
     * @return Turno
     */
    public function setComprobante(\AdminBundle\Entity\Comprobante $comprobante = null)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return \AdminBundle\Entity\Comprobante
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }
}
