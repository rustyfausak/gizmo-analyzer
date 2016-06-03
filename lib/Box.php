<?php

namespace Gizmo;

class Box
{
	/**
	 * @param Point $max
	 * @param Point $min
	 */
	public function __construct(Point $max, Point $min)
	{
		$this->max = $max;
		$this->min = $min;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return 'Box(' . $this->min . ', ' . $this->max . ')';
	}

	/**
	 * @param Point $point
	 */
	public function expandTo(Point $point)
	{
		$this->max->maxWith($point);
		$this->min->minWith($point);
	}
}
