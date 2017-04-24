<?php
// src/AppBundle/Entity/User.php

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @UniqueEntity("email",errorPath="email",groups={"Registracion"})
 * @UniqueEntity("username",errorPath="username",groups={"Registracion"})
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var $usuarioSede
     *
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\UsuarioSede", mappedBy="usuario")
     */
    private $usuarioSede;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\Login", mappedBy="usuario")
     */
    private $login;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\Turno", mappedBy="usuarioConfirmacion")
     */
    private $turnoConfirmacion;


    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ColaTurno", mappedBy="usuarioAtendido")
     */
    private $colaTurnoAtendio;

    /**
     * @var $usuarioTipoTramite
     *
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\UsuarioTurnoSede", mappedBy="usuario")
     */
    private $usuarioTipoTramite;



    public function __construct()
    {
        parent::__construct();

    }


    /**
     * Set usuarioSede
     *
     * @param \AdminBundle\Entity\UsuarioSede $usuarioSede
     *
     * @return User
     */
    public function setUsuarioSede(\AdminBundle\Entity\UsuarioSede $usuarioSede = null)
    {
        $this->usuarioSede = $usuarioSede;

        return $this;
    }

    /**
     * Get usuarioSede
     *
     * @return \AdminBundle\Entity\UsuarioSede
     */
    public function getUsuarioSede()
    {
        return $this->usuarioSede;
    }

    /**
     * Add login
     *
     * @param \AdminBundle\Entity\Login $login
     *
     * @return User
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
     * Add turnoConfirmacion
     *
     * @param \UserBundle\Entity\User $turnoConfirmacion
     *
     * @return User
     */
    public function addTurnoConfirmacion(\UserBundle\Entity\User $turnoConfirmacion)
    {
        $this->turnoConfirmacion[] = $turnoConfirmacion;

        return $this;
    }

    /**
     * Remove turnoConfirmacion
     *
     * @param \UserBundle\Entity\User $turnoConfirmacion
     */
    public function removeTurnoConfirmacion(\UserBundle\Entity\User $turnoConfirmacion)
    {
        $this->turnoConfirmacion->removeElement($turnoConfirmacion);
    }

    /**
     * Get turnoConfirmacion
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnoConfirmacion()
    {
        return $this->turnoConfirmacion;
    }

    /**
     * Add colaTurnoAtendio
     *
     * @param \AdminBundle\Entity\ColaTurno $colaTurnoAtendio
     *
     * @return User
     */
    public function addColaTurnoAtendio(\AdminBundle\Entity\ColaTurno $colaTurnoAtendio)
    {
        $this->colaTurnoAtendio[] = $colaTurnoAtendio;

        return $this;
    }

    /**
     * Remove colaTurnoAtendio
     *
     * @param \AdminBundle\Entity\ColaTurno $colaTurnoAtendio
     */
    public function removeColaTurnoAtendio(\AdminBundle\Entity\ColaTurno $colaTurnoAtendio)
    {
        $this->colaTurnoAtendio->removeElement($colaTurnoAtendio);
    }

    /**
     * Get colaTurnoAtendio
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColaTurnoAtendio()
    {
        return $this->colaTurnoAtendio;
    }

    /**
     * Set usuarioTipoTramite
     *
     * @param \AdminBundle\Entity\UsuarioTurnoSede $usuarioTipoTramite
     *
     * @return User
     */
    public function setUsuarioTipoTramite(\AdminBundle\Entity\UsuarioTurnoSede $usuarioTipoTramite = null)
    {
        $this->usuarioTipoTramite = $usuarioTipoTramite;

        return $this;
    }

    /**
     * Get usuarioTipoTramite
     *
     * @return \AdminBundle\Entity\UsuarioTurnoSede
     */
    public function getUsuarioTipoTramite()
    {
        return $this->usuarioTipoTramite;
    }

    /**
     * Add usuarioTipoTramite
     *
     * @param \AdminBundle\Entity\UsuarioTurnoSede $usuarioTipoTramite
     *
     * @return User
     */
    public function addUsuarioTipoTramite(\AdminBundle\Entity\UsuarioTurnoSede $usuarioTipoTramite)
    {
        $this->usuarioTipoTramite[] = $usuarioTipoTramite;

        return $this;
    }

    /**
     * Remove usuarioTipoTramite
     *
     * @param \AdminBundle\Entity\UsuarioTurnoSede $usuarioTipoTramite
     */
    public function removeUsuarioTipoTramite(\AdminBundle\Entity\UsuarioTurnoSede $usuarioTipoTramite)
    {
        $this->usuarioTipoTramite->removeElement($usuarioTipoTramite);
    }
}
