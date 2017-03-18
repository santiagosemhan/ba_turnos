<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="turno_sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TurnosSedeRepository")
 */
class TurnosSede extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $lunes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $martes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $miercoles;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $jueves;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $viernes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sabado;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $horaTurnosDesde;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $horaTurnosHasta;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantidadTurnos;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantidadFrecuencia;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $frecunciaTurnoControl;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $vigenciaDesde;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $vigenciaHasta;

    /**
     * @ORM\OneToMany(targetEntity="TurnoTramite", mappedBy="turnosSede")
     */
    private $turnoTramite;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="turnosSede")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;
}