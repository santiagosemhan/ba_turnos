<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="sede_tipo_tramite")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SedeTipoTramiteRepository")
 */
class SedeTipoTramite extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sede", inversedBy="sedeTipoTramite")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id", nullable=false, unique=true)
     */
    private $sede;

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="TipoTramite", inversedBy="sedeTipoTramite")
     * @ORM\JoinColumn(name="tipo_tramite_id", referencedColumnName="id", unique=true)
     */
    private $tipoTramite;
}