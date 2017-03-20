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
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="accion",type="string", nullable=true)
     */
    private $accion;

    /**
     * @ORM\Column(name="asunto",type="string", length=120, nullable=true)
     */
    private $asunto;

    /**
     * @ORM\Column(name="texto",type="blob", nullable=true)
     */
    private $texto;

    /**
     * @ORM\OneToMany(targetEntity="Mail", mappedBy="textoMail")
     */
    private $mail;
}