<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Defuse\Crypto\Crypto;
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
    private $turnoId;

    /**
     * @ORM\Column(name="letra",type="string", nullable=true)
     */
    private $letra;

    /**
     * @ORM\Column(name="numero",type="string", nullable=true)
     */
    private $numero;

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
     * Set turnoId
     *
     * @param \AdminBundle\Entity\Turno $turnoId
     *
     * @return Comprobante
     */
    public function setTurnoId(\AdminBundle\Entity\Turno $turnoId = null)
    {
        $this->turnoId = $turnoId;

        return $this;
    }

    /**
     * Get turnoId
     *
     * @return \AdminBundle\Entity\Turno
     */
    public function getTurnoId()
    {
        return $this->turnoId;
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
            $this->getTurnoId()->getId().'%'.
            $this->getLetra().'#'.
            $this->getNumero().'&'.
            $this->getTipoTramite();
        return Crypto::encrypt($texto, $this->getSecretKey());
        //return \SaferCrypto::encrypt($texto,$this->getSecretKey());
    }
}
