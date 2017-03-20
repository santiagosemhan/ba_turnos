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
     * @ORM\Column(name="texto",type="string", nullable=true)
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
}