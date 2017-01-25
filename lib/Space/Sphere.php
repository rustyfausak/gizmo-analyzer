<?php

namespace Gizmo\Space;

class Sphere extends Hitbox
{
	/**
	 * @param float $radius
	 */
	public function __construct($radius)
	{
		$this->radius = $radius;
		$this->origin = null;
	}

	public function update($location, $rotation)
	{
		//$this->origin = $location;
		$this->origin = clone $location;
		$this->origin->translate(Vector::withArr([$this->radius, $this->radius, 0]));
	}
}
