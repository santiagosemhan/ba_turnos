<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="sede")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SedeRepository")
 */
class Sede extends BaseClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="sede",type="string", length=2000, nullable=true)
     */
    private $sede;

    /**
     * @ORM\Column(name="direccion",nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(name="letra",type="string", length=2, nullable=true)
     */
    private $letra;

    /**
     * @ORM\Column(name="ultimo_turno",type="integer", length=10, nullable=true)
     */
    private $ultimoTurno;

    /**
     * @ORM\OneToMany(targetEntity="SedeTipoTramite", mappedBy="sede")
     */
    private $sedeTipoTramite;

    /**
     * @ORM\OneToMany(targetEntity="Feriado", mappedBy="sede")
     */
    private $feriado;

    /**
     * @ORM\OneToMany(targetEntity="Box", mappedBy="sede")
     */
    private $box;

    /**
     * @ORM\OneToMany(targetEntity="CancelacionMasiva", mappedBy="sede")
     */
    private $cancelacionMasiva;

    /**
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="sede")
     */
    private $turno;

    /**
     * @ORM\OneToMany(targetEntity="Login", mappedBy="sede")
     */
    private $login;

    /**
     * @ORM\OneToMany(targetEntity="UsuarioSede", mappedBy="sede")
     */
    private $usuarioSede;

    /**
     * @ORM\OneToMany(targetEntity="TurnosSede", mappedBy="sede")
     */
    private $turnosSede;

    /**
     * @ORM\OneToMany(targetEntity="ColaTurno", mappedBy="sede")
     */
    private $colaTurno;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feriado = new \Doctrine\Common\Collections\ArrayCollection();
        $this->box = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cancelacionMasiva = new \Doctrine\Common\Collections\ArrayCollection();
        $this->turno = new \Doctrine\Common\Collections\ArrayCollection();
        $this->login = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usuarioSede = new \Doctrine\Common\Collections\ArrayCollection();
        $this->turnosSede = new \Doctrine\Common\Collections\ArrayCollection();
        $this->colaTurno = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set sede
     *
     * @param string $sede
     *
     * @return Sede
     */
    public function setSede($sede)
    {
        $this->sede = $sede;

        return $this;
    }

    /**
     * Get sede
     *
     * @return string
     */
    public function getSede()
    {
        return $this->sede;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Sede
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set letra
     *
     * @param string $letra
     *
     * @return Sede
     */
    public function setLetra($letra)
    {
        $this->letra = $letra;

        return $this;
    }

    /**
     * Get letra
     *
     * @return string
     */
    public function getLetra()
    {
        return $this->letra;
    }

    /**
     * Set ultimoTurno
     *
     * @param integer $ultimoTurno
     *
     * @return Sede
     */
    public function setUltimoTurno($ultimoTurno)
    {
        $this->ultimoTurno = $ultimoTurno;

        return $this;
    }

    /**
     * Get ultimoTurno
     *
     * @return integer
     */
    public function getUltimoTurno()
    {
        return $this->ultimoTurno;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Sede
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
     * @return Sede
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
     * @return Sede
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
     * Add feriado
     *
     * @param \AdminBundle\Entity\Feriado $feriado
     *
     * @return Sede
     */
    public function addFeriado(\AdminBundle\Entity\Feriado $feriado)
    {
        $this->feriado[] = $feriado;

        return $this;
    }

    /**
     * Remove feriado
     *
     * @param \AdminBundle\Entity\Feriado $feriado
     */
    public function removeFeriado(\AdminBundle\Entity\Feriado $feriado)
    {
        $this->feriado->removeElement($feriado);
    }

    /**
     * Get feriado
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeriado()
    {
        return $this->feriado;
    }

    /**
     * Add box
     *
     * @param \AdminBundle\Entity\Box $box
     *
     * @return Sede
     */
    public function addBox(\AdminBundle\Entity\Box $box)
    {
        $this->box[] = $box;

        return $this;
    }

    /**
     * Remove box
     *
     * @param \AdminBundle\Entity\Box $box
     */
    public function removeBox(\AdminBundle\Entity\Box $box)
    {
        $this->box->removeElement($box);
    }

    /**
     * Get box
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * Add cancelacionMasiva
     *
     * @param \AdminBundle\Entity\CancelacionMasiva $cancelacionMasiva
     *
     * @return Sede
     */
    public function addCancelacionMasiva(\AdminBundle\Entity\CancelacionMasiva $cancelacionMasiva)
    {
        $this->cancelacionMasiva[] = $cancelacionMasiva;

        return $this;
    }

    /**
     * Remove cancelacionMasiva
     *
     * @param \AdminBundle\Entity\CancelacionMasiva $cancelacionMasiva
     */
    public function removeCancelacionMasiva(\AdminBundle\Entity\CancelacionMasiva $cancelacionMasiva)
    {
        $this->cancelacionMasiva->removeElement($cancelacionMasiva);
    }

    /**
     * Get cancelacionMasiva
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCancelacionMasiva()
    {
        return $this->cancelacionMasiva;
    }

    /**
     * Add turno
     *
     * @param \AdminBundle\Entity\Turno $turno
     *
     * @return Sede
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
     * Add login
     *
     * @param \AdminBundle\Entity\Login $login
     *
     * @return Sede
     */
    public function addLogin(\AdminBundle\Entity\Login $login)
    {
        $this->login[] = $login;

        return $this;
    }

    /**
     * Remove login
     *
     * @param \AdminBundle\Entity\Login $login
     */
    public function removeLogin(\AdminBundle\Entity\Login $login)
    {
        $this->login->removeElement($login);
    }

    /**
     * Get login
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Add usuarioSede
     *
     * @param \AdminBundle\Entity\UsuarioSede $usuarioSede
     *
     * @return Sede
     */
    public function addUsuarioSede(\AdminBundle\Entity\UsuarioSede $usuarioSede)
    {
        $this->usuarioSede[] = $usuarioSede;

        return $this;
    }

    /**
     * Remove usuarioSede
     *
     * @param \AdminBundle\Entity\UsuarioSede $usuarioSede
     */
    public function removeUsuarioSede(\AdminBundle\Entity\UsuarioSede $usuarioSede)
    {
        $this->usuarioSede->removeElement($usuarioSede);
    }

    /**
     * Get usuarioSede
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarioSede()
    {
        return $this->usuarioSede;
    }

    /**
     * Add turnosSede
     *
     * @param \AdminBundle\Entity\TurnosSede $turnosSede
     *
     * @return Sede
     */
    public function addTurnosSede(\AdminBundle\Entity\TurnosSede $turnosSede)
    {
        $this->turnosSede[] = $turnosSede;

        return $this;
    }

    /**
     * Remove turnosSede
     *
     * @param \AdminBundle\Entity\TurnosSede $turnosSede
     */
    public function removeTurnosSede(\AdminBundle\Entity\TurnosSede $turnosSede)
    {
        $this->turnosSede->removeElement($turnosSede);
    }

    /**
     * Get turnosSede
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnosSede()
    {
        return $this->turnosSede;
    }

    /**
     * Add colaTurno
     *
     * @param \AdminBundle\Entity\ColaTurno $colaTurno
     *
     * @return Sede
     */
    public function addColaTurno(\AdminBundle\Entity\ColaTurno $colaTurno)
    {
        $this->colaTurno[] = $colaTurno;

        return $this;
    }

    /**
     * Remove colaTurno
     *
     * @param \AdminBundle\Entity\ColaTurno $colaTurno
     */
    public function removeColaTurno(\AdminBundle\Entity\ColaTurno $colaTurno)
    {
        $this->colaTurno->removeElement($colaTurno);
    }

    /**
     * Get colaTurno
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColaTurno()
    {
        return $this->colaTurno;
    }

    /**
     * Set creadoPor
     *
     * @param \UserBundle\Entity\User $creadoPor
     *
     * @return Sede
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
     * @return Sede
     */
    public function setActualizadoPor(\UserBundle\Entity\User $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    public function __toString()
    {
        return $this->getSede();
    }
}
