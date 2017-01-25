<?php

namespace Gizmo\Game;

use \Gizmo\Space\Vector;
use \Gizmo\Space\Rotation;

class Property
{
	public $_name;
	public $_type;

	/**
	 * @param string $name
	 * @param string $type
	 * @param mixed $data
	 */
	public function __construct($name, $type, $data)
	{
		$this->_name = $name;
		$this->_type = $type;
		$this->update($data);
	}

	/**
	 * @param mixed $data
	 */
	public function update($data)
	{
		switch ($this->_type) {
			case 'Boolean':
			case 'Byte':
			case 'Enum':
			case 'Float':
			case 'Int':
			case 'String':
				$this->value = $data;
				break;
			case 'CamSettings':
				$this->fov = array_shift($data);
				$this->height = array_shift($data);
				$this->angle = array_shift($data);
				$this->distance = array_shift($data);
				$this->stiffness = array_shift($data);
				$this->swivel_speed = array_shift($data);
				break;
			case 'Demolish':
				$this->flag1 = array_shift($data);
				$this->demolisher = array_shift($data);
				$this->flag2 = array_shift($data);
				$this->demolishee = array_shift($data);
				$this->vector1 = Vector::withArr(array_shift($data));
				$this->vector2 = Vector::withArr(array_shift($data));
				break;
			case 'Explosion':
				$this->flag1 = array_shift($data);
				$this->force = array_shift($data);
				$this->location = Vector::withArr(array_shift($data));
				break;
			case 'FlaggedInt':
				$this->flag = array_shift($data);
				$this->value = array_shift($data);
				break;
			case 'GameMode':
				break;
			case 'LoadoutOnline':
				$this->version = array_shift($data);
				$this->unknown1 = array_shift($data);
				$this->unknown2 = array_shift($data);
				$this->unknown3 = array_shift($data);
				break;
			case 'Loadout':
				$this->version = array_shift($data);
				$this->car_id = array_shift($data);
				$this->decal_id = array_shift($data);
				$this->wheel_id = array_shift($data);
				$this->trail_id = array_shift($data);
				$this->antenna_id = array_shift($data);
				$this->topper_id = array_shift($data);
				$this->unknown1 = array_shift($data);
				if ($this->version >= 11) {
					$this->unknown2 = array_shift($data);
				}
				break;
			case 'Location':
				$this->value = Vector::withArr($data);
				break;
			case 'MusicStinger':
				$this->flag = array_shift($data);
				$this->unknown1 = array_shift($data);
				$this->unknown2 = array_shift($data);
				break;
			case 'Pickup':
				$this->flag1 = array_shift($data);
				$this->actor_id = array_shift($data);
				$this->flag2 = array_shift($data);
				break;
			case 'PrivateMatchSettings':
				break;
			case 'QWord':
				$this->unknown1 = array_shift($data);
				$this->unknown2 = array_shift($data);
				break;
			case 'RelativeRotation':
				break;
			case 'Reservation':
				$this->unknown1 = array_shift($data);
				$this->unknown2 = array_shift($data);
				$details = array_shift($data);
				if ($details && sizeof($details)) {
					$this->player_uid_type = $details['tag'];
					$this->player_uid = $details['contents'];
				}
				$this->player_name = array_shift($data);
				$this->flag1 = array_shift($data);
				$this->flag2 = array_shift($data);
				break;
			case 'RigidBodyState':
				$check = array_shift($data);
				$this->location = Vector::withArr(array_shift($data));
				$this->rotation = Rotation::withScaledArr(array_shift($data));
				if (!$check) {
					$this->linear_velocity = Vector::withArr(array_shift($data));
					$this->angular_velocity = Vector::withArr(array_shift($data));
				}
				break;
			case 'TeamPaint':
				$this->unknown1 = array_shift($data);
				$this->unknown2 = array_shift($data);
				$this->unknown3 = array_shift($data);
				$this->unknown4 = array_shift($data);
				$this->unknown5 = array_shift($data);
				break;
			case 'UniqueId':
				$this->unknown1 = array_shift($data);
				$details = array_shift($data);
				if ($details && sizeof($details)) {
					$this->player_uid_type = $details['tag'];
					$this->player_uid = $details['contents'];
				}
				$this->unknown2 = array_shift($data);
				break;

			default:
				break;
		}
	}
}
