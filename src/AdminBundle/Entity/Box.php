<?php

namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="box")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\BoxRepository")
 */
class Box extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity="ColaTurno", mappedBy="box")
     */
    private $colaTurno;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="box")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     */
    private $sede;
}