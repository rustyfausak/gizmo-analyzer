<?php

namespace Gizmo\Space;

class Rotation
{
	/* @var int */
	public $pitch;
	/* @var int */
	public $yaw;
	/* @var int */
	public $roll;

	/**
	 * @param int $pitch  0-255
	 * @param int $yaw  0-255
	 * @param int $roll  0-255
	 */
	public function __construct($pitch = 0, $yaw = 0, $roll = 0)
	{
		$this->pitch = $pitch;
		$this->yaw = $yaw;
		$this->roll = $roll;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return __CLASS__ . '(' . $this->pitch . ', ' . $this->yaw . ', ' . $this->roll . ')';
	}

	/**
	 * @param array $arr
	 * @return Rotation
	 */
	public static function withArr($arr)
	{
		return new self(
			array_shift($arr),
			array_shift($arr),
			array_shift($arr)
		);
	}

	/**
	 * Values are scaled between -1 and 1.
	 *
	 * @param array $arr
	 * @return Rotation
	 */
	public static function withScaledArr($arr)
	{
		$arr = array_map(function ($n) {
			return min(255, max(0, min(2, $n + 1)) * 128);
		}, $arr);
		return self::withArr($arr);
	}
}
