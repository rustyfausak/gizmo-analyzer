<?php

namespace Gizmo\Space;

class Rotation
{
	/* @var float degrees */
	public $pitch;
	/* @var float degrees */
	public $yaw;
	/* @var float degrees */
	public $roll;

	/**
	 * @param int $pitch  0-360 degrees
	 * @param int $yaw  0-360 degrees
	 * @param int $roll  0-360 degrees
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
		return 'rot(' . $this->pitch . ', ' . $this->yaw . ', ' . $this->roll . ')';
	}

	/**
	 * @return array
	 */
	public function asArray()
	{
		return [$this->pitch, $this->yaw, $this->roll];
	}

	/**
	 * @param array $arr
	 * @return Rotation
	 */
	public static function withArr($arr)
	{
		return new self(
			array_shift($arr) * -1,
			array_shift($arr),
			array_shift($arr) * -1
		);
	}

	/**
	 * Values are scaled between 0 and 255.
	 *
	 * @param array $arr
	 * @return Rotation
	 */
	public static function with255Arr($arr)
	{
		return self::withArr(array_map(function ($n) {
			return $n / 255 * 360;
		}, $arr));
	}

	/**
	 * Values are scaled between -1 and 1.
	 *
	 * @param array $arr
	 * @return Rotation
	 */
	public static function withScaledArr($arr)
	{
		return self::withArr(array_map(function ($n) {
			return max(0, min(2, $n + 1)) * 180;
		}, $arr));
	}
}
