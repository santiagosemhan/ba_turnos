<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SedeRepository")
 */
class Sede extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="sede",type="string", length=2000, nullable=true)
     */
    private $sede;

    /**
     * @ORM\Column(name="direccion",nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(name="letra",type="string", length=2, nullable=true)
     */
    private $letra;

    /**
     * @ORM\Column(name="ultimo_turno",type="integer", length=10, nullable=true)
     */
    private $ultimoTurno;

    /**
     * @ORM\OneToOne(targetEntity="SedeTipoTramite", mappedBy="sede")
     */
    private $sedeTipoTramite;

    /**
     * @ORM\OneToMany(targetEntity="Feriado", mappedBy="sede")
     */
    private $feriado;

    /**
     * @ORM\OneToMany(targetEntity="Box", mappedBy="sede")
     */
    private $box;

    /**
     * @ORM\OneToMany(targetEntity="CancelacionMasiva", mappedBy="sede")
     */
    private $cancelacionMasiva;

    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="sede")
     */
    private $turno;

    /**
     * @ORM\OneToMany(targetEntity="Login", mappedBy="sede")
     */
    private $login;

    /**
     * @ORM\OneToMany(targetEntity="UsuarioSede", mappedBy="sede")
     */
    private $usuarioSede;

    /**
     * @ORM\OneToMany(targetEntity="TurnosSede", mappedBy="sede")
     */
    private $turnosSede;

    /**
     * @ORM\OneToMany(targetEntity="ColaTurno", mappedBy="sede")
     */
    private $colaTurno;
}