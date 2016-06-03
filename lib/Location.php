<?php

namespace Gizmo;

class Location
{
	/**
	 * @param Point $point
	 * @param float $time
	 */
	public function __construct(Point $point, $time)
	{
		$this->point = $point;
		$this->time = $time;
	}
}
