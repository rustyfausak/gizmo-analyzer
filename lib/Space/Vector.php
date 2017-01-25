<?php

namespace Gizmo\Space;

use \NumPHP\Core\NumArray;

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
		return 'vec(' . $this->x . ', ' . $this->y . ', ' . $this->z . ')';
	}

	/**
	 * @return array
	 */
	public function asArray()
	{
		return [$this->x, $this->y, $this->z];
	}

	/**
	 * @param array $arr
	 */
	public function set(array $arr)
	{
		$this->x = array_shift($arr);
		$this->y = array_shift($arr);
		$this->z = array_shift($arr);
	}

	/**
	 * Returns the distance to the point $b.
	 *
	 * @param Vector $b
	 * @return float
	 */
	public function distanceTo(Vector $b)
	{
		return self::distance($this, $b);
	}

	/**
	 * @param Vector $pt
	 */
	public function translate($pt)
	{
		$this->x += $pt->x;
		$this->y += $pt->y;
		$this->z += $pt->z;
		return $this;
	}

	/**
	 * @param Rotation $rotation
	 */
	public function orient($rotation)
	{
		$b = deg2rad($rotation->pitch); // pitch
		$a = deg2rad($rotation->yaw); // yaw
		$g = deg2rad($rotation->roll); // roll
		$pt = new NumArray($this->asArray());
		$r = new NumArray([
			[
				cos($a) * cos($b),
				cos($a) * sin($b) * sin($g) - sin($a) * cos($g),
				cos($a) * sin($b) * cos($g) + sin($a) * sin($g)
			],
			[
				sin($a) * cos($b),
				sin($a) * sin($b) * sin($g) + cos($a) * cos($g),
				sin($a) * sin($b) * cos($g) - cos($a) * sin($g)
			],
			[
				-1 * sin($b),
				cos($b) * sin($g),
				cos($b) * cos($g)
			]
		]);
		$prod = $r->dot($pt);
		$this->set($prod->getData());
	}

	/**
	 * @param float $deg
	 */
	public function pitch($deg)
	{
		$rad = deg2rad($deg);
		$pt = new NumArray($this->asArray());
		$r = new NumArray([
			[cos($rad), 0, sin($rad)],
			[0, 1, 0],
			[-1 * sin($rad), 0, cos($rad)]
		]);
		$prod = $r->dot($pt);
		$this->set($prod->getData());
	}

	/**
	 * @param float $deg
	 */
	public function yaw($deg)
	{
		$rad = deg2rad($deg);
		$pt = new NumArray($this->asArray());
		$r = new NumArray([
			[cos($rad), -1 * sin($rad), 0],
			[sin($rad), cos($rad), 0],
			[0, 0, 1]
		]);
		$prod = $r->dot($pt);
		$this->set($prod->getData());
	}

	/**
	 * @param float $deg
	 */
	public function roll($deg)
	{
		$rad = deg2rad($deg);
		$pt = new NumArray($this->asArray());
		$r = new NumArray([
			[1, 0, 0],
			[0, cos($rad), -1 * sin($rad)],
			[0, sin($rad), cos($rad)]
		]);
		$prod = $r->dot($pt);
		$this->set($prod->getData());
	}

	/**
	 * @param array $arr
	 * @return Vector
	 */
	public static function withArr(array $arr)
	{
		return new self(
			array_shift($arr),
			array_shift($arr),
			array_shift($arr)
		);
	}

	/**
	 * Returns the distance between the two points.
	 *
	 * @param Vector $a
	 * @param Vector $b
	 * @return float
	 */
	public static function distance(Vector $a, Vector $b)
	{
		return sqrt(pow($b->x - $a->x, 2) + pow($b->y - $a->y, 2) + pow($b->z - $a->z, 2));
	}
}
