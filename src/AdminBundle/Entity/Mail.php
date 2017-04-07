<?php
namespace AdminBundle\Entity;


use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="mail")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\MailRepository")
 */
class Mail extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="email",type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(name="nombre",type="string", nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(name="asunto",type="string", nullable=true)
     */
    private $asunto;

    /**
     * @ORM\Column(name="texto",type="string",length=100000, nullable=true)
     */
    private $texto;

    /**
     * @ORM\Column(name="enviado",type="boolean", nullable=true)
     */
    private $enviado;

    /**
     * @ORM\Column(name="fecha_programado",type="date", nullable=true)
     */
    private $fechaProgramado;

    /**
     * @ORM\Column(name="fecha_enviado",type="date", nullable=true)
     */
    private $fechaEnviado;

    /**
     * @ORM\ManyToOne(targetEntity="Turno", inversedBy="mail")
     * @ORM\JoinColumn(name="turno_id", referencedColumnName="id")
     */
    private $turno;

    /**
     * @ORM\ManyToOne(targetEntity="TextoMail", inversedBy="mail")
     * @ORM\JoinColumn(name="texto_mail_id", referencedColumnName="id")
     */
    private $textoMail;

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
     * Set email
     *
     * @param string $email
     *
     * @return Mail
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Mail
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set asunto
     *
     * @param string $asunto
     *
     * @return Mail
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;

        return $this;
    }

    /**
     * Get asunto
     *
     * @return string
     */
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * Set texto
     *
     * @param string $texto
     *
     * @return Mail
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set enviado
     *
     * @param boolean $enviado
     *
     * @return Mail
     */
    public function setEnviado($enviado)
    {
        $this->enviado = $enviado;

        return $this;
    }

    /**
     * Get enviado
     *
     * @return boolean
     */
    public function getEnviado()
    {
        return $this->enviado;
    }

    /**
     * Set fechaProgramado
     *
     * @param \DateTime $fechaProgramado
     *
     * @return Mail
     */
    public function setFechaProgramado($fechaProgramado)
    {
        $this->fechaProgramado = $fechaProgramado;

        return $this;
    }

    /**
     * Get fechaProgramado
     *
     * @return \DateTime
     */
    public function getFechaProgramado()
    {
        return $this->fechaProgramado;
    }

    /**
     * Set fechaEnviado
     *
     * @param \DateTime $fechaEnviado
     *
     * @return Mail
     */
    public function setFechaEnviado($fechaEnviado)
    {
        $this->fechaEnviado = $fechaEnviado;

        return $this;
    }

    /**
     * Get fechaEnviado
     *
     * @return \DateTime
     */
    public function getFechaEnviado()
    {
        return $this->fechaEnviado;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Mail
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
     * @return Mail
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
     * @return Mail
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
     * Set textoMail
     *
     * @param \AdminBundle\Entity\TextoMail $textoMail
     *
     * @return Mail
     */
    public function setTextoMail(\AdminBundle\Entity\TextoMail $textoMail = null)
    {
        $this->textoMail = $textoMail;

        return $this;
    }

    /**
     * Get textoMail
     *
     * @return \AdminBundle\Entity\TextoMail
     */
    public function getTextoMail()
    {
        return $this->textoMail;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return Mail
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
     * @return Mail
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
