<?php

namespace Gizmo\Space;

class Rect
{
	/**
	 * @param int $p0
	 * @param int $p1
	 * @param int $p2
	 * @param int $p3
	 */
	public function __construct(Vector $p0, Vector $p1, Vector $p2, Vector $p3)
	{
		$this->p0 = $p0;
		$this->p1 = $p1;
		$this->p2 = $p2;
		$this->p3 = $p3;
	}

	public function getPoints()
	{
		return [$this->p0, $this->p1, $this->p2, $this->p3];
	}

	public function getMinX()
	{
		$v = null;
		foreach ($this->getPoints() as $p) {
			if ($v === null || $p->x < $v) {
				$v = $p->x;
			}
		}
		return $v;
	}

	public function getMaxX()
	{
		$v = null;
		foreach ($this->getPoints() as $p) {
			if ($v === null || $p->x > $v) {
				$v = $p->x;
			}
		}
		return $v;
	}

	public function getMinY()
	{
		$v = null;
		foreach ($this->getPoints() as $p) {
			if ($v === null || $p->y < $v) {
				$v = $p->y;
			}
		}
		return $v;
	}

	public function getMaxY()
	{
		$v = null;
		foreach ($this->getPoints() as $p) {
			if ($v === null || $p->y > $v) {
				$v = $p->y;
			}
		}
		return $v;
	}

	public static function axisAligned($min_x, $max_x, $min_y, $max_y)
	{
		return new self(
			Vector::withArr([$min_x, $min_y]),
			Vector::withArr([$max_x, $min_y]),
			Vector::withArr([$max_x, $max_y]),
			Vector::withArr([$min_x, $max_y])
		);
	}
}
