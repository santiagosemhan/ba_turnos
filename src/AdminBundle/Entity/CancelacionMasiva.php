<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="cancelacion_masiva")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\CancelacionMasivaRepository")
 */
class CancelacionMasiva extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2000, nullable=false)
     */
    private $motivo;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="cancelacionMasiva")
     */
    private $turno;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="cancelacionMasiva")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;
}