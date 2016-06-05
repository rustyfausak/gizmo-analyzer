<?php

namespace Gizmo\Space;

class Vector
{
	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 */
	public function __construct($x = 0, $y = 0, $z = 0)
	{
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return __CLASS__ . '(' . $this->x . ', ' . $this->y . ', ' . $this->z . ')';
	}

	/**
	 * @param array $arr
	 * @return Vector
	 */
	public static function withArr($arr)
	{
		return new self(
			array_shift($arr),
			array_shift($arr),
			array_shift($arr)
		);
	}
}
