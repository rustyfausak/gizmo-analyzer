<?php

namespace Gizmo\Space;

class ImageXYZ
{
	/**
	 * @param int $px
	 * @param int $field_size
	 */
	public function __construct($px, $field_size)
	{
		$this->padding = 15;
		$this->canvas = imagecreatetruecolor($px * 2 + $this->padding * 3, $px + $this->padding * 3);
		$this->scale = $px / $field_size;
		$half_field_size = round($field_size / 2);
		$this->views = [
			'x' => [
				'origin_x' => $half_field_size * 3 * $this->scale + $this->padding * 2,
				'origin_y' => $half_field_size * $this->scale + $this->padding
			],
			'y' => [
				'origin_x' => $half_field_size * 3 * $this->scale + $this->padding * 2,
				'origin_y' => $field_size * $this->scale + $this->padding * 2
			],
			'z' => [
				'origin_x' => $half_field_size * $this->scale + round($this->padding),
				'origin_y' => $half_field_size * $this->scale  + round($this->padding * 1.5)
			]
		];
		$border_color = $this->createColor(255, 255, 255);
		$text_color = $this->createColor(0, 255, 0);
		$text_shift_up = ($this->padding + 2) / $this->scale;
		$this->view_drawText('x', 'x', -1 * $half_field_size, $half_field_size + $text_shift_up, $text_color);
		$this->view_drawText('y', 'y', -1 * $half_field_size, $half_field_size + $text_shift_up, $text_color);
		$this->view_drawText('z', 'z', -1 * $half_field_size + $this->padding, (-1 * $half_field_size) - $text_shift_up, $text_color);
		$this->view_drawRect('x', Rect::axisAligned(-1 * $half_field_size, $half_field_size, 0, $half_field_size), $border_color);
		$this->view_drawRect('y', Rect::axisAligned(-1 * $half_field_size, $half_field_size, 0, $half_field_size), $border_color);
		$this->view_drawRect('z', Rect::axisAligned(-1 * $half_field_size, $half_field_size, -1 * $half_field_size, $half_field_size), $border_color);
	}

	/**
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @return int
	 */
	public function createColor($r, $g, $b)
	{
		return imagecolorallocate($this->canvas, $r, $g, $b);
	}

	/**
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name, $args)
	{
		if (preg_match('/^all_(.+?)$/', $name, $m)) {
			foreach (['x', 'y', 'z'] as $name) {
				$params = $args;
				array_unshift($params, $name);
				call_user_func_array(array($this, 'view_' . $m[1]), $params);
			}
		}
	}

	/**
	 * @param string $name
	 * @param Sphere $sphere
	 * @param int $color
	 */
	public function view_drawSphere($name, Sphere $sphere, $color)
	{
		$diameter = $sphere->radius * 2;
		switch ($name) {
			case 'x':
				$this->view_drawCircle($name, Vector::withArr([$sphere->origin->y, $sphere->origin->z]), $diameter, $color);
				break;
			case 'y':
				$this->view_drawCircle($name, Vector::withArr([$sphere->origin->x, $sphere->origin->z]), $diameter, $color);
				break;
			case 'z':
			default:
				$this->view_drawCircle($name, $sphere->origin, $diameter, $color);
				break;

		}
	}

	/**
	 * @param string $name
	 * @param Box $box
	 * @param int $color1
	 * @param int $color2
	 * @param int $color3
	 */
	public function view_drawBox($name, Box $box, $color1, $color2, $color3)
	{
		list($r1, $r2) = $box->viewRectsAlongAxis($name);
		$this->view_drawRect($name, $r2, $color2);
		$this->view_drawLine($name, $r1->p0, $r2->p0, $color3);
		$this->view_drawLine($name, $r1->p1, $r2->p1, $color3);
		$this->view_drawLine($name, $r1->p2, $r2->p2, $color3);
		$this->view_drawLine($name, $r1->p3, $r2->p3, $color3);
		$this->view_drawRect($name, $r1, $color1);
	}

	/**
	 * @param Vector $origin
	 * @param int $diameter
	 * @param int $color
	 */
	public function drawCircle($origin, $diameter, $color)
	{
		imageellipse($this->canvas, $origin->x, $origin->y, $diameter, $diameter, $color);
	}

	/**
	 * @param string $name
	 * @param Vector $origin
	 * @param int $diameter
	 * @param int $color
	 */
	public function view_drawCircle($name, $origin, $diameter, $color)
	{
		$this->drawCircle(
			Vector::withArr([
				$this->mapX($name, $origin->x),
				$this->mapY($name, $origin->y)
			]),
			$diameter * $this->scale,
			$color
		);
	}

	/**
	 * @param string $text
	 * @param int $x
	 * @param int $y
	 * @param int $color
	 */
	public function drawText($text, $x, $y, $color)
	{
		imagestring($this->canvas, 5, $x, $y, $text, $color);
	}

	/**
	 * @param string $text
	 * @param int $view_x
	 * @param int $view_y
	 * @param int $color
	 */
	public function view_drawText($name, $text, $view_x, $view_y, $color)
	{
		$this->drawText($text, $this->mapX($name, $view_x), $this->mapY($name, $view_y), $color);
	}

	/**
	 * @param Rect $rect
	 * @param int $color
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
	 * @param string $name
	 * @param Rect $rect
	 * @param int $color
	 */
	public function view_drawRect($name, Rect $rect, $color)
	{
		$this->drawRect(new Rect(
			Vector::withArr([$this->mapX($name, $rect->p0->x), $this->mapY($name, $rect->p0->y)]),
			Vector::withArr([$this->mapX($name, $rect->p1->x), $this->mapY($name, $rect->p1->y)]),
			Vector::withArr([$this->mapX($name, $rect->p2->x), $this->mapY($name, $rect->p2->y)]),
			Vector::withArr([$this->mapX($name, $rect->p3->x), $this->mapY($name, $rect->p3->y)])
		), $color);
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
			$a->x, $a->y,
			$b->x, $b->y,
			$color
		);
	}

	/**
	 * @param string $name
	 * @param Vector $a
	 * @param Vector $b
	 * @param int $color
	 */
	public function view_drawLine($name, Vector $a, Vector $b, $color)
	{
		$this->drawLine(
			Vector::withArr([$this->mapX($name, $a->x), $this->mapY($name, $a->y)]),
			Vector::withArr([$this->mapX($name, $b->x), $this->mapY($name, $b->y)]),
			$color
		);
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $color
	 */
	public function drawPoint($x, $y, $color)
	{
		imagesetpixel($this->canvas, $x, $y, $color);
	}

	/**
	 * @param string $name
	 * @param int $view_x
	 * @param int $view_y
	 * @param int $color
	 */
	public function view_drawPoint($name, $view_x, $view_y, $color)
	{
		$this->drawPoint($this->mapX($name, $view_x), $this->mapY($name, $view_y), $color);
	}

	/**
	 * @param string $name
	 * @param mixed $view_x
	 * @return int
	 */
	public function mapX($name, $view_x)
	{
		return round($view_x * $this->scale + $this->views[$name]['origin_x']);
	}

	/**
	 * @param string $name
	 * @param mixed $y
	 * @return int
	 */
	public function mapY($name, $view_y)
	{
		switch ($name) {
			case 'y':
			case 'x':
				return round($view_y * -1 * $this->scale + $this->views[$name]['origin_y']);
			default:
			case 'z':
				return round($view_y * $this->scale + $this->views[$name]['origin_y']);
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
