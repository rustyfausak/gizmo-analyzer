<?php

namespace Gizmo\Game;

class Team
{
	/**
	 */
	public function __construct()
	{
		$this->color = null;
	}

	/**
	 * @param string $color
	 */
	public function setColor($color)
	{
		$this->color = $color;
	}

	public function getColor()
	{
		return $this->color;
	}
}
