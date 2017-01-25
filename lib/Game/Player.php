<?php

namespace Gizmo\Game;

use \Gizmo\Space\Box;

class Player
{
	use RigidBody;

	/**
	 */
	public function __construct()
	{
		$this->initRigidBody();
		$this->pri_actor_id = null;
		$this->team_actor_id = null;
		$this->name = null;
	}

	/**
	 * @param Frame $frame
	 * @param Actor $actor
	 */
	public function updatePRI($frame, $actor)
	{
		$this->pri_actor_id = $actor->id;

		if (!$this->hitbox) {
			$loadout_property = $actor->getProperty('TAGame.PRI_TA:ClientLoadout');
			if ($loadout_property) {
				$this->hitbox = $this->getHitboxFromCarId($loadout_property->car_id);
			}
		}
		if ($this->team_actor_id === null) {
			$team_property = $actor->getProperty('Engine.PlayerReplicationInfo:Team');
			if ($team_property) {
				$this->team_actor_id = $team_property->value;
			}
		}
		if ($this->name === null) {
			$name_property = $actor->getProperty('Engine.PlayerReplicationInfo:PlayerName');
			if ($name_property) {
				$this->name = $name_property->value;
			}
		}
	}

	/**
	 * @param int $car_id
	 */
	public function getHitboxFromCarId($car_id)
	{
		switch ($car_id) {
			case 21: // Backfire
				return new Box(117.1566, 84.67036, 31.39440);
			case 803: // Batmobile
				return new Box(128.8198, 84.67036, 29.39440);
			case 22: // Breakout
				return new Box(128.9930, 76.36520, 30.30000);
			case 597: // DeLorean
				return new Box(123.9424, 83.27995, 29.80000);
			case 403: // Dominus
				return new Box(127.9268, 83.27995, 31.30000);
			case 26: // Gizmo
				return new Box(122.7370, 83.25725, 37.16797);
			case 607: // Grog
				return new Box(119.4957, 81.87707, 36.92777);
			case 29: // Hotshot
				return new Box(123.9424, 77.76891, 31.80000);
			case 30: // Merc
				return new Box(119.9868, 77.97530, 40.26958);
			case 23: // Octane
				return new Box(118.0074, 84.19941, 36.15907);
			case 24: // Paladin
				return new Box(123.2510, 76.88635, 29.80000);
			case 600: // Ripper
				return new Box(127.5825, 79.09612, 31.43038);
			case 25: // Road Hog
				return new Box(117.1566, 84.67036, 31.39440);
			case 404: // Scarab
				return new Box(114.9007, 82.74691, 37.66797);
			case 402: // Takumi
				return new Box(118.4945, 80.27252, 34.30000);
			case 28: // X-Devil
				return new Box(127.6995, 81.88242, 31.80000);
			case 31: // Venom
				return new Box(119.0544, 85.74410, 34.76144);
			case 523: // Zippy
				return new Box(118.0074, 84.19941, 33.15907);
		}
	}
}
