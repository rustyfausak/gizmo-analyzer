<?php

namespace Gizmo\Models;

use Gizmo\Models\Base\Property as BaseProperty;

/**
 * Skeleton subclass for representing a row from the 'properties' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Property extends BaseProperty
{
	public function update($type, $data)
	{
		$type = substr($type, 1);
		switch ($type) {
			case 'Boolean':
				$this->setValueBoolean($data);
				break;
			case 'Byte':
			case 'Int':
				$this->setValueInt($data);
				break;
			case 'String':
				$this->setValueString($data);
				break;
			case 'Float':
				$this->setValueFloat($data);
				break;
			case 'CamSettings':
			case 'Demolish':
			case 'Enum':
			case 'Explosion':
			case 'FlaggedInt':
			case 'GameMode':
			case 'LoadoutOnline':
			case 'Loadout':
			case 'Location':
			case 'MusicStinger':
			case 'Pickup':
			case 'PrivateMatchSettings':
			case 'QWord':
			case 'RelativeRotation':
			case 'RigidBodyState':
			case 'TeamPaint':
			case 'UniqueId':
			default:

		}
	}
}
