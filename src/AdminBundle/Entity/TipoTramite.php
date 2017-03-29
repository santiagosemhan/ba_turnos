<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="tipo_tramite")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TipoTramiteRepository")
 */
class TipoTramite extends BaseClass implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="descripcion",type="string", length=120, nullable=false)
     */
    private $descripcion;

    /**
     * @ORM\Column(name="texto",type="string", length=2000, nullable=true)
     */
    private $texto;

    /**
     * @var bool
     *
     * @ORM\Column(name="sin_turno", type="boolean")
     */
    protected $sinTurno = false;

    /**
     * @ORM\Column(name="documento1",type="string", length=120, nullable=true)
     */
    private $documento1;

    /**
     * @ORM\Column(name="documento2",type="string", length=120, nullable=true)
     */
    private $documento2;

    /**
     * @ORM\Column(name="documento3",type="string", length=120, nullable=true)
     */
    private $documento3;

    /**
     * @ORM\Column(name="documento4",type="string", length=120, nullable=true)
     */
    private $documento4;

    /**
     * @ORM\Column(name="documento5",type="string", length=120, nullable=true)
     */
    private $documento5;

    /**
     * @ORM\Column(name="documento6",type="string", length=120, nullable=true)
     */
    private $documento6;

    /**
     * @ORM\Column(name="slug",type="string", length=50, nullable=false)
     */
    private $slug;

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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->turno = new \Doctrine\Common\Collections\ArrayCollection();
        $this->turnoTramite = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return TipoTramite
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set texto
     *
     * @param string $texto
     *
     * @return TipoTramite
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set sinTurno
     *
     * @param boolean $sinTurno
     *
     * @return TipoTramite
     */
    public function setSinTurno($sinTurno)
    {
        $this->sinTurno = $sinTurno;

        return $this;
    }

    /**
     * Get sinTurno
     *
     * @return boolean
     */
    public function getSinTurno()
    {
        return $this->sinTurno;
    }

    /**
     * Set documento1
     *
     * @param string $documento1
     *
     * @return TipoTramite
     */
    public function setDocumento1($documento1)
    {
        $this->documento1 = $documento1;

        return $this;
    }

    /**
     * Get documento1
     *
     * @return string
     */
    public function getDocumento1()
    {
        return $this->documento1;
    }

    /**
     * Set documento2
     *
     * @param string $documento2
     *
     * @return TipoTramite
     */
    public function setDocumento2($documento2)
    {
        $this->documento2 = $documento2;

        return $this;
    }

    /**
     * Get documento2
     *
     * @return string
     */
    public function getDocumento2()
    {
        return $this->documento2;
    }

    /**
     * Set documento3
     *
     * @param string $documento3
     *
     * @return TipoTramite
     */
    public function setDocumento3($documento3)
    {
        $this->documento3 = $documento3;

        return $this;
    }

    /**
     * Get documento3
     *
     * @return string
     */
    public function getDocumento3()
    {
        return $this->documento3;
    }

    /**
     * Set documento4
     *
     * @param string $documento4
     *
     * @return TipoTramite
     */
    public function setDocumento4($documento4)
    {
        $this->documento4 = $documento4;

        return $this;
    }

    /**
     * Get documento4
     *
     * @return string
     */
    public function getDocumento4()
    {
        return $this->documento4;
    }

    /**
     * Set documento5
     *
     * @param string $documento5
     *
     * @return TipoTramite
     */
    public function setDocumento5($documento5)
    {
        $this->documento5 = $documento5;

        return $this;
    }

    /**
     * Get documento5
     *
     * @return string
     */
    public function getDocumento5()
    {
        return $this->documento5;
    }

    /**
     * Set documento6
     *
     * @param string $documento6
     *
     * @return TipoTramite
     */
    public function setDocumento6($documento6)
    {
        $this->documento6 = $documento6;

        return $this;
    }

    /**
     * Get documento6
     *
     * @return string
     */
    public function getDocumento6()
    {
        return $this->documento6;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return TipoTramite
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return TipoTramite
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Set fechaActualizacion
     *
     * @param \DateTime $fechaActualizacion
     *
     * @return TipoTramite
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set sedeTipoTramite
     *
     * @param \AdminBundle\Entity\SedeTipoTramite $sedeTipoTramite
     *
     * @return TipoTramite
     */
    public function setSedeTipoTramite(\AdminBundle\Entity\SedeTipoTramite $sedeTipoTramite = null)
    {
        $this->sedeTipoTramite = $sedeTipoTramite;

        return $this;
    }

    /**
     * Get sedeTipoTramite
     *
     * @return \AdminBundle\Entity\SedeTipoTramite
     */
    public function getSedeTipoTramite()
    {
        return $this->sedeTipoTramite;
    }

    /**
     * Add turno
     *
     * @param \AdminBundle\Entity\Turno $turno
     *
     * @return TipoTramite
     */
    public function addTurno(\AdminBundle\Entity\Turno $turno)
    {
        $this->turno[] = $turno;

        return $this;
    }

    /**
     * Remove turno
     *
     * @param \AdminBundle\Entity\Turno $turno
     */
    public function removeTurno(\AdminBundle\Entity\Turno $turno)
    {
        $this->turno->removeElement($turno);
    }

    /**
     * Get turno
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * Add turnoTramite
     *
     * @param \AdminBundle\Entity\TurnoTramite $turnoTramite
     *
     * @return TipoTramite
     */
    public function addTurnoTramite(\AdminBundle\Entity\TurnoTramite $turnoTramite)
    {
        $this->turnoTramite[] = $turnoTramite;

        return $this;
    }

    /**
     * Remove turnoTramite
     *
     * @param \AdminBundle\Entity\TurnoTramite $turnoTramite
     */
    public function removeTurnoTramite(\AdminBundle\Entity\TurnoTramite $turnoTramite)
    {
        $this->turnoTramite->removeElement($turnoTramite);
    }

    /**
     * Get turnoTramite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnoTramite()
    {
        return $this->turnoTramite;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return TipoTramite
     */
    public function setCreadoPor(\UserBundle\Entity\User $creadoPor = null)
    {
        $this->creadoPor = $creadoPor;

        return $this;
    }

    /**
     * Set actualizadoPor
     *
     * @param \UserBundle\Entity\User $actualizadoPor
     *
     * @return TipoTramite
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }


    public function jsonSerialize()
    {
        return [
          'id' => $this->getId(),
          'descripcion'=>$this->getDescripcion(),
          'texto'=> $this->getTexto()
        ];
    }
}
