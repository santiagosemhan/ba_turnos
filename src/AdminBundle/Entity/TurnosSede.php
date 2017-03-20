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
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="lunes",type="boolean", nullable=true)
     */
    private $lunes;

    /**
     * @ORM\Column(name="martes",type="boolean", nullable=true)
     */
    private $martes;

    /**
     * @ORM\Column(name="miercoles",type="boolean", nullable=true)
     */
    private $miercoles;

    /**
     * @ORM\Column(name="jueves",type="boolean", nullable=true)
     */
    private $jueves;

    /**
     * @ORM\Column(name="viernes",type="boolean", nullable=true)
     */
    private $viernes;

    /**
     * @ORM\Column(name="sabado",type="boolean", nullable=true)
     */
    private $sabado;

    /**
     * @ORM\Column(name="hora_turnos_desde",type="string", nullable=true)
     */
    private $horaTurnosDesde;

    /**
     * @ORM\Column(name="hora_turnos_hasta",type="string", nullable=true)
     */
    private $horaTurnosHasta;

    /**
     * @ORM\Column(name="cantidad_turnos",type="integer", nullable=true)
     */
    private $cantidadTurnos;

    /**
     * @ORM\Column(name="cantidad_frecuencia",type="integer", nullable=true)
     */
    private $cantidadFrecuencia;

    /**
     * @ORM\Column(name="frecuncia_turno_control",type="integer", nullable=true)
     */
    private $frecunciaTurnoControl;

    /**
     * @ORM\Column(name="vigencia_desde",type="date", nullable=true)
     */
    private $vigenciaDesde;

    /**
     * @ORM\Column(name="vigencia_hasta",type="date", nullable=true)
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