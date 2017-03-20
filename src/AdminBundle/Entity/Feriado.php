<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="feriado")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\FeriadoRepository")
 */
class Feriado extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="fecha",type="date", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(name="repite_anio",type="boolean", nullable=true)
     */
    private $repiteAnio;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="feriado")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;
}