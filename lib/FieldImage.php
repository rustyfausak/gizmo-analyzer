<?php

namespace Gizmo;

class FieldImage
{
	/**
	 * @param int $px
	 * @param Box $box  Field bounding box
	 */
	public function __construct($px, Box $box)
	{
		$this->padding = 10;
		$this->canvas = imagecreatetruecolor($px + $this->padding * 2, $px + $this->padding * 2);
		$this->offset_x = -1 * $box->min->x;
		$this->offset_y = -1 * $box->min->y;
		$this->scale = $px / max($box->max->x - $box->min->x, $box->max->y - $box->min->y);
		$this->drawBox($box);
	}

	/**
	 * @param float|int $x
	 * @return int
	 */
	public function mapX($x)
	{
		return intval(($x + $this->offset_x) * $this->scale + $this->padding);
	}

	/**
	 * @param float|int $y
	 * @return int
	 */
	public function mapY($y)
	{
		return intval(($y + $this->offset_y) * $this->scale + $this->padding);
	}

	/**
	 * @param Box $box
	 */
	public function drawBox(Box $box)
	{
		$color = imagecolorallocate($this->canvas, 255, 255, 255);
		$this->drawLine(
			Point::withXY($box->min->x, $box->min->y),
			Point::withXY($box->max->x, $box->min->y),
			$color
		);
		$this->drawLine(
			Point::withXY($box->max->x, $box->min->y),
			Point::withXY($box->max->x, $box->max->y),
			$color
		);
		$this->drawLine(
			Point::withXY($box->max->x, $box->max->y),
			Point::withXY($box->min->x, $box->max->y),
			$color
		);
		$this->drawLine(
			Point::withXY($box->min->x, $box->max->y),
			Point::withXY($box->min->x, $box->min->y),
			$color
		);
	}

	/**
	 * @param Point $a
	 * @param Point $b
	 * @param int $color
	 */
	public function drawLine(Point $a, Point $b, $color)
	{
		imageline(
			$this->canvas,
			$this->mapX($a->x), $this->mapY($a->y),
			$this->mapX($b->x), $this->mapY($b->y),
			$color
		);
	}

	/**
	 * @param Point $p
	 * @param int $color
	 */
	public function drawPoint(Point $p, $color)
	{
		imagesetpixel($this->canvas, $this->mapX($p->x), $this->mapY($p->y), $color);
	}

	/**
	 * @param Actor $actor
	 * @param int $color
	 */
	public function drawActor(Actor $actor, $color = null)
	{
		if (!$color) {
			$color = imagecolorallocate($this->canvas, 0, 255, 0);
		}
		foreach ($actor->locations as $location) {
			$this->drawPoint($location->point, $color);
		}
	}

	/**
	 * @param string $path
	 */
	public function output($path)
	{
		imagepng($this->canvas, $path);
		imagedestroy($this->canvas);
	}
}
