<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use UserBundle\Entity\User;

/**
 * @ORM\Table(name="login")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\LoginRepository")
 */
class Login extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $nombre_pc;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="Login")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="Login")
     * @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     */
    private $user;
}