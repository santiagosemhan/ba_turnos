<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="turno_tramite")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TurnoTramiteRepository")
 */
class TurnoTramite extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantidadTurno;

    /**
     * @ORM\ManyToOne(targetEntity="TipoTramite", inversedBy="turnoTramite")
     * @ORM\JoinColumn(name="tipo_tramite_id", referencedColumnName="id")
     */
    private $tipoTramite;

    /**
     * @ORM\ManyToOne(targetEntity="TurnosSede", inversedBy="turnoTramite")
     * @ORM\JoinColumn(name="turno_ssede_id", referencedColumnName="id")
     */
    private $turnosSede;
}