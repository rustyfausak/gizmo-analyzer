<?php

namespace Gizmo\Space;

abstract class Hitbox
{
	public function intersects($other)
	{
		return false;
	}

	abstract public function update($location, $rotation);
}
