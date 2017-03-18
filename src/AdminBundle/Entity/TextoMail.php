<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="texto_mail")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TextoMailRepository")
 */
class TextoMail extends BaseClass
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
    private $accion;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $asunto;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $texto;

    /**
     * @ORM\OneToMany(targetEntity="Mail", mappedBy="textoMail")
     */
    private $mail;
}