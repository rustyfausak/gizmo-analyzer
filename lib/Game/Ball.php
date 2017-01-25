<?php

namespace Gizmo\Game;

use \Gizmo\Space\Sphere;

class Ball
{
	use RigidBody;

	/**
	 */
	public function __construct()
	{
		$this->initRigidBody(new Sphere(88));
	}
}
