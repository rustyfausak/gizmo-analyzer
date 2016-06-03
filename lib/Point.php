<?php

namespace Gizmo;

class Point
{
	/**
	 * @param array $pos
	 */
	public function __construct(array $pos)
	{
		$this->x = $pos[0];
		$this->y = $pos[1];
		$this->z = $pos[2];
	}

	/**
	 * @param int $x
	 * @param int $y
	 */
	public static function withXY($x, $y)
	{
		return new Point([$x, $y, 0]);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return 'Point(' . $this->x . ', ' . $this->y . ', ' . $this->z . ')';
	}

	/**
	 * @param Point $point
	 */
	public function maxWith(Point $point)
	{
		$this->x = max($this->x, $point->x);
		$this->y = max($this->y, $point->y);
		$this->z = max($this->z, $point->z);
	}

	/**
	 * @param Point $point
	 */
	public function minWith(Point $point)
	{
		$this->x = min($this->x, $point->x);
		$this->y = min($this->y, $point->y);
		$this->z = min($this->z, $point->z);
	}

	/**
	 * Returns the distance to the Point $b.
	 *
	 * @param Point $b
	 * @return float
	 */
	public function distanceTo(Point $b)
	{
		return self::distance($this, $b);
	}

	/**
	 * Returns the distance between the two Points.
	 *
	 * @param Point $a
	 * @param Point $b
	 * @return float
	 */
	public static function distance(Point $a, Point $b)
	{
		return sqrt(pow($b->x - $a->x, 2) + pow($b->y - $a->y, 2) + pow($b->z - $a->z, 2));
	}
}
