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
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $asunto;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $texto;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enviado;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaProgramado;

    /**
     * @ORM\Column(type="date", nullable=true)
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