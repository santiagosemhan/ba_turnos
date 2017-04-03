<?php

namespace UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use UserBundle\Entity\User;

/**
 * The order.placed event is dispatched each time an order is created
 * in the system.
 */
class UsuarioPasswordModificadoEvent extends Event
{
	const NAME = 'usuario.password.modificado';

	protected $usuario;

	protected $plainPass;

	public function __construct(User $usuario)
	{
		$this->usuario = $usuario;
	}

	public function getUsuario()
	{
		return $this->usuario;
	}
}