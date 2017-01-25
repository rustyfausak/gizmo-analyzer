<?php

namespace Gizmo\Space;

class Box extends Hitbox
{
	/**
	 * @param float $length
	 * @param float $width
	 * @param float $height
	 */
	public function __construct($length, $width, $height)
	{
		$this->length = $length;
		$this->width = $width;
		$this->height = $height;
		$this->origin = null;
		$this->p1 = null;
		$this->p2 = null;
		$this->p3 = null;
		$this->p4 = null;
		$this->p5 = null;
		$this->p6 = null;
		$this->p7 = null;
		$this->p8 = null;
	}

	public function viewRectsAlongAxis($axis)
	{
		switch (strtolower($axis)) {
			case 'z':
				return [
					new Rect($this->top_front_right, $this->top_front_left, $this->top_back_left, $this->top_back_right),
					new Rect($this->bot_front_right, $this->bot_front_left, $this->bot_back_left, $this->bot_back_right)
				];
			case 'y':
				return [
					new Rect(
						Vector::withArr([$this->top_front_left->x, $this->top_front_left->z]),
						Vector::withArr([$this->top_back_left->x, $this->top_back_left->z]),
						Vector::withArr([$this->bot_back_left->x, $this->bot_back_left->z]),
						Vector::withArr([$this->bot_front_left->x, $this->bot_front_left->z])
					),
					new Rect(
						Vector::withArr([$this->top_front_right->x, $this->top_front_right->z]),
						Vector::withArr([$this->top_back_right->x, $this->top_back_right->z]),
						Vector::withArr([$this->bot_back_right->x, $this->bot_back_right->z]),
						Vector::withArr([$this->bot_front_right->x, $this->bot_front_right->z])
					)
				];
			case 'x':
				return [
					new Rect(
						Vector::withArr([$this->top_front_right->y, $this->top_front_right->z]),
						Vector::withArr([$this->top_front_left->y, $this->top_front_left->z]),
						Vector::withArr([$this->bot_front_left->y, $this->bot_front_left->z]),
						Vector::withArr([$this->bot_front_right->y, $this->bot_front_right->z])
					),
					new Rect(
						Vector::withArr([$this->top_back_right->y, $this->top_back_right->z]),
						Vector::withArr([$this->top_back_left->y, $this->top_back_left->z]),
						Vector::withArr([$this->bot_back_left->y, $this->bot_back_left->z]),
						Vector::withArr([$this->bot_back_right->y, $this->bot_back_right->z])
					)
				];
		}
	}

	/**
	 * @param Vector $location
	 * @param Rotation $rotation
	 */
	public function update($location, $rotation = null)
	{
		$this->origin = $location;

		// set points with world origin
		$this->top_front_right = new Vector(
			$this->length / 2,
			$this->width / 2,
			$this->height / 2
		);
		$this->top_front_left = new Vector(
			$this->length / 2,
			-1 * $this->width / 2,
			$this->height / 2
		);
		$this->top_back_right = new Vector(
			-1 * $this->length / 2,
			$this->width / 2,
			$this->height / 2
		);
		$this->top_back_left = new Vector(
			-1 * $this->length / 2,
			-1 * $this->width / 2,
			$this->height / 2
		);
		$this->bot_front_right = new Vector(
			$this->length / 2,
			$this->width / 2,
			-1 * $this->height / 2
		);
		$this->bot_front_left = new Vector(
			$this->length / 2,
			-1 * $this->width / 2,
			-1 * $this->height / 2
		);
		$this->bot_back_right = new Vector(
			-1 * $this->length / 2,
			$this->width / 2,
			-1 * $this->height / 2
		);
		$this->bot_back_left = new Vector(
			-1 * $this->length / 2,
			-1 * $this->width / 2,
			-1 * $this->height / 2
		);

		if ($rotation) {
			// do rotation
			$this->top_front_right->orient($rotation);
			$this->top_front_left->orient($rotation);
			$this->top_back_right->orient($rotation);
			$this->top_back_left->orient($rotation);
			$this->bot_front_right->orient($rotation);
			$this->bot_front_left->orient($rotation);
			$this->bot_back_right->orient($rotation);
			$this->bot_back_left->orient($rotation);
		}

		// translate to object origin
		$this->top_front_right->translate($this->origin);
		$this->top_front_left->translate($this->origin);
		$this->top_back_right->translate($this->origin);
		$this->top_back_left->translate($this->origin);
		$this->bot_front_right->translate($this->origin);
		$this->bot_front_left->translate($this->origin);
		$this->bot_back_right->translate($this->origin);
		$this->bot_back_left->translate($this->origin);
	}
}
