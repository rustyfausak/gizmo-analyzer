<?php

namespace Gizmo\Properties;

class RigidBodyStateProperty extends Property {
	public function __construct($name, $data)
	{
		$this->name = $name;
		$this->data['location'] = \Gizmo\Space\Vector::withArr($data[1]);
		$this->data['rotation'] = \Gizmo\Space\Rotation::withScaledArr($data[2]);
		if (!$data[0]) {
			$this->data['linear_velocity'] = \Gizmo\Space\Vector::withArr($data[3]);
			$this->data['angular_velocity'] = \Gizmo\Space\Vector::withArr($data[4]);
		}
	}
}
