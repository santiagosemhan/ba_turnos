<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="tipo_tramite")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TipoTramiteRepository")
 * @Vich\Uploadable
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
     * @ORM\Column(name="texto",type="text", nullable=true)
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
     * @ORM\Column(name="slug",type="string", length=50, nullable=true)
     */
    private $slug;


    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="tipoTramite")
     */
    private $turno;

    /**
     * @ORM\OneToMany(targetEntity="TurnoTipoTramite", mappedBy="tipoTramite")
     */
    private $turnoTipoTramite;


    /**
     * @ORM\ManyToOne(targetEntity="OpcionGeneral", inversedBy="tiposTramites")
     * @ORM\JoinColumn(name="opcion_general_id", referencedColumnName="id")
     */
    private $opcionGeneral;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\File(
     *     maxSize="10M"
     * )
     *
     * @Vich\UploadableField(mapping="tramites_file", fileNameProperty="documento1")
     *
     * @var File
     */
    private $documento1File;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\File(
     *     maxSize="10M"
     * )
     *
     * @Vich\UploadableField(mapping="tramites_file", fileNameProperty="documento2")
     *
     * @var File
     */
    private $documento2File;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\File(
     *     maxSize="10M"
     * )
     *
     * @Vich\UploadableField(mapping="tramites_file", fileNameProperty="documento3")
     *
     * @var File
     */
    private $documento3File;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\File(
     *     maxSize="10M"
     * )
     *
     * @Vich\UploadableField(mapping="tramites_file", fileNameProperty="documento4")
     *
     * @var File
     */
    private $documento4File;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\File(
     *     maxSize="10M"
     * )
     *
     * @Vich\UploadableField(mapping="tramites_file", fileNameProperty="documento5")
     *
     * @var File
     */
    private $documento5File;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\File(
     *     maxSize="10M"
     * )
     *
     * @Vich\UploadableField(mapping="tramites_file", fileNameProperty="documento6")
     *
     * @var File
     */
    private $documento6File;

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

    /** Set slug
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


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param UploadedFile  $file
     *
     * @return UploadedFile
     */
    public function setDocumento1File(File $file = null)
    {
        $this->documento1File = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
        if ($file instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getDocumento1File()
    {
        return $this->documento1File;
    }

    /**
     * @param UploadedFile $file
     *
     * @return UploadedFile
     */
    public function setDocumento2File(File $file = null)
    {
        $this->documento2File = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getDocumento2File()
    {
        return $this->documento2File;
    }

    /**
     * @param UploadedFile $file
     *
     * @return UploadedFile
     */
    public function setDocumento3File(File $file = null)
    {
        $this->documento3File = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getDocumento3File()
    {
        return $this->documento3File;
    }

    /**
     * @param UploadedFile $file
     *
     * @return UploadedFile
     */
    public function setDocumento4File(File $file = null)
    {
        $this->documento4File = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getDocumento4File()
    {
        return $this->documento4File;
    }

    /**
     * @param UploadedFile $file
     *
     * @return UploadedFile
     */
    public function setDocumento5File(File $file = null)
    {
        $this->documento5File = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getDocumento5File()
    {
        return $this->documento5File;
    }

    /**
     * @param UploadedFile $file
     *
     * @return UploadedFile
     */
    public function setDocumento6File(File $file = null)
    {
        $this->documento6File = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getDocumento6File()
    {
        return $this->documento6File;
    }

    /**
     * toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getOpcionGeneral() ? $this->getOpcionGeneral()->getDescripcion().' - '.$this->getDescripcion() : '';
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'descripcion'=>$this->getDescripcion(),
            'texto'=> $this->getTexto(),
            'file' => $this->getPathFiles(),
        ];
    }

    public function getPathFiles()
    {
        $array = array();
        if ($this->documento1) {
            $array[substr(strstr($this->documento1, '_'),1)] = 'documento1File';
        }
        if ($this->documento2) {
            $array[substr(strstr($this->documento2, '_'),1)] = 'documento2File';
        }
        if ($this->documento3) {
            $array[substr(strstr($this->documento3, '_'),1)] = 'documento3File';
        }
        if ($this->documento4) {
            $array[substr(strstr($this->documento4, '_'),1)] = 'documento4File';
        }
        if ($this->documento5) {
            $array[substr(strstr($this->documento5, '_'),1)] = 'documento5File';
        }
        if ($this->documento6) {
            $array[substr(strstr($this->documento6, '_'),1)] = 'documento6File';
        }
        //how to use:
        //$helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        //$path = $helper->asset(Tipo Tramite $entity, $array[$index]);
        return $array;
    }

    /**
     * Add turnoTipoTramite
     *
     * @param \AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite
     *
     * @return TipoTramite
     */
    public function addTurnoTipoTramite(\AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite)
    {
        $this->turnoTipoTramite[] = $turnoTipoTramite;

        return $this;
    }

    /**
     * Remove turnoTipoTramite
     *
     * @param \AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite
     */
    public function removeTurnoTipoTramite(\AdminBundle\Entity\TurnoTipoTramite $turnoTipoTramite)
    {
        $this->turnoTipoTramite->removeElement($turnoTipoTramite);
    }

    /**
     * Get turnoTipoTramite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnoTipoTramite()
    {
        return $this->turnoTipoTramite;
    }

    /**
     * Set opcionGeneral
     *
     * @param \AdminBundle\Entity\OpcionGeneral $opcionGeneral
     *
     * @return TipoTramite
     */
    public function setOpcionGeneral(\AdminBundle\Entity\OpcionGeneral $opcionGeneral = null)
    {
        $this->opcionGeneral = $opcionGeneral;
        return $this;
    }

    /**
     * Get opcionesGenerales
     *
     * @return \AdminBundle\Entity\OpcionGeneral
     */
    public function getOpcionGeneral()
    {
        return $this->opcionGeneral;
    }
}
