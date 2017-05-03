<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(
 *     name="comprobante",
 *     indexes={
 *         @ORM\Index(name="Index1", columns={"id"})
 *     })
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ComprobanteRepository")
 */
class Comprobante extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Turno", inversedBy="comprobante",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="turno_id", referencedColumnName="id")
     *
     */
    private $turno;

    /**
     * @ORM\Column(name="letra",type="string", nullable=true)
     */
    private $letra;

    /**
     * @ORM\Column(name="numero",type="string", nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(name="hora",type="string", nullable=true)
     */
    private $hora;

    /**
     * @ORM\Column(name="fecha",type="string", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(name="tipo_tramite",type="string", nullable=true)
     */
    private $tipoTramite;

    /**
     * @ORM\Column(name="sede",type="string", nullable=true)
     */
    private $sede;

    /**
     * @ORM\Column(name="hash_control",type="string", nullable=true)
     */
    private $hashControl;

    private $secretKey;

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
     * Set letra
     *
     * @param string $letra
     *
     * @return Comprobante
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
     * @param string $numero
     *
     * @return Comprobante
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set tipoTramite
     *
     * @param string $tipoTramite
     *
     * @return Comprobante
     */
    public function setTipoTramite($tipoTramite)
    {
        $this->tipoTramite = $tipoTramite;

        return $this;
    }

    /**
     * Get tipoTramite
     *
     * @return string
     */
    public function getTipoTramite()
    {
        return $this->tipoTramite;
    }

    /**
     * Set sede
     *
     * @param string $sede
     *
     * @return Comprobante
     */
    public function setSede($sede)
    {
        $this->sede = $sede;

        return $this;
    }

    /**
     * Get sede
     *
     * @return string
     */
    public function getSede()
    {
        return $this->sede;
    }

    /**
     * Set hashControl
     *
     * @param string $hashControl
     *
     * @return Comprobante
     */
    public function setHashControl($hashControl)
    {
        $this->hashControl = $hashControl;

        return $this;
    }

    /**
     * Get hashControl
     *
     * @return string
     */
    public function getHashControl()
    {
        return $this->hashControl;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return Comprobante
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
     * @return Comprobante
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function setSecretKey($secretKey)
    {
        return $this->secretKey = $secretKey;
    }

    public function getHash()
    {
        $texto =  $this->getId().'$'.
            $this->getTurno()->getId().'%'.
            $this->getLetra().'#'.
            $this->getNumero().'&'.
            $this->getTipoTramite();
        return Crypto::encrypt($texto,Key::loadFromAsciiSafeString($this->getSecretKey()));
    }

    /**
     * Set hora
     *
     * @param string $hora
     *
     * @return Comprobante
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get hora
     *
     * @return string
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set fecha
     *
     * @param string $fecha
     *
     * @return Comprobante
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return string
     */
    public function getFecha()
    {
        return $this->fecha;
    }
}
