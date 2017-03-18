<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="tipo_tramite")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TipoTramiteRepository")
 */
class TipoTramite extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120, nullable=false)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $texto;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $documento1;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $documento2;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $documento3;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $documento4;

    /**
     * @ORM\OneToOne(targetEntity="SedeTipoTramite", mappedBy="tipoTramite")
     */
    private $sedeTipoTramite;

    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="tipoTramite")
     */
    private $turno;

    /**
     * @ORM\OneToMany(targetEntity="TurnoTramite", mappedBy="tipoTramite")
     */
    private $turnoTramite;
}