<?php

namespace Gizmo\Space;

class Image
{
	/**
	 * @param int $px
	 * @param int $min_x
	 * @param int $max_x
	 * @param int $min_y
	 * @param int $max_y
	 */
	public function __construct($px, Rect $field)
	{
		$this->padding = 10;
		$this->canvas = imagecreatetruecolor($px + $this->padding * 2, $px + $this->padding * 2);
		$this->offset_x = -1 * $field->getMinX();
		$this->offset_y = -1 * $field->getMinY();
		$this->scale = $px / max($field->getMaxX() - $field->getMinX(), $field->getMaxY() - $field->getMinY());
		$this->drawRect($field, $this->createColor(255, 255, 255));
	}

	public function createColor($r, $g, $b)
	{
		return imagecolorallocate($this->canvas, $r, $g, $b);
	}

	/**
	 * @param Sphere $sphere
	 * @param string $axis_viewpoint
	 * @param int $color
	 */
	public function drawSphere(Sphere $sphere, $axis_viewpoint, $color = null)
	{
		if (!$color) {
			$color = $this->createColor(255, 0, 255);
		}
		$diameter = $sphere->radius * 2;
		switch (strtolower($axis_viewpoint)) {
			case 'x':
				$this->drawCircle(Vector::withArr([$sphere->origin->y, $sphere->origin->z]), $diameter, $color);
				break;
			case 'y':
				$this->drawCircle(Vector::withArr([$sphere->origin->x, $sphere->origin->z]), $diameter, $color);
				break;
			case 'z':
			default:
				$this->drawCircle($sphere->origin, $diameter, $color);
				break;

		}
	}

	public function drawBox(Box $box, $axis_viewpoint, $color1 = null, $color2 = null, $color3 = null)
	{
		if (!$color1) {
			$color1 = $this->createColor(255, 200, 0);
		}
		if (!$color2) {
			$color2 = $this->createColor(100, 76, 0);
		}
		if (!$color3) {
			$color3 = $this->createColor(100, 100, 100);
		}
		list($r1, $r2) = $box->viewRectsAlongAxis($axis_viewpoint);
		$this->drawRect($r2, $color2);
		$this->drawLine($r1->p0, $r2->p0, $color3);
		$this->drawLine($r1->p1, $r2->p1, $color3);
		$this->drawLine($r1->p2, $r2->p2, $color3);
		$this->drawLine($r1->p3, $r2->p3, $color3);
		$this->drawRect($r1, $color1);
	}

	public function drawCircle($origin, $diameter, $color)
	{
		imageellipse($this->canvas, $this->mapX($origin->x), $this->mapY($origin->y), $diameter * $this->scale, $diameter * $this->scale, $color);
	}

	/**
	 * @param Rect $rect
	 */
	public function drawRect(Rect $rect, $color)
	{
		$this->drawLine(
			$rect->p0,
			$rect->p1,
			$color
		);
		$this->drawLine(
			$rect->p1,
			$rect->p2,
			$color
		);
		$this->drawLine(
			$rect->p2,
			$rect->p3,
			$color
		);
		$this->drawLine(
			$rect->p3,
			$rect->p0,
			$color
		);
	}

	/**
	 * @param Vector $a
	 * @param Vector $b
	 * @param int $color
	 */
	public function drawLine(Vector $a, Vector $b, $color)
	{
		imageline(
			$this->canvas,
			$this->mapX($a->x), $this->mapY($a->y),
			$this->mapX($b->x), $this->mapY($b->y),
			$color
		);
	}

	public function drawText($text, $x, $y, $color = null)
	{
		if (!$color) {
			$color = $this->createColor(255, 255, 255);
		}
		imagestring($this->canvas, 1, $this->mapX($x), $this->mapY($y), $text, $color);
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
		return intval((-1 * $y + $this->offset_y) * $this->scale + $this->padding);
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
