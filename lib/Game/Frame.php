<?php

namespace Gizmo\Game;

class Frame
{
	public $number;
	public $time;
	public $delta;

	/**
	 * @param int $number
	 * @param float $time
	 * @param float $delta
	 */
	public function __construct($number, $time, $delta)
	{
		$this->number = $number;
		$this->time = $time;
		$this->delta = $delta;
	}

	public function __toString()
	{
		return "Frame #{$this->number} @ {$this->time} ({$this->delta})";
	}
}
