<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="usuario_sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\UsuarioSedeRepository")
 */
class UsuarioSede extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="usuarioSede")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="usuarioSede")
     * @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     */
    private $user;
}