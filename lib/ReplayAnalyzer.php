<?php

namespace Gizmo;

class ReplayAnalyzer
{
	public $data;
	public $actors;

	/**
	 * @param stdClass $data  Replay data
	 */
	public function __construct($data)
	{
		$this->data = $data;
		$this->build();
	}

	/**
	 */
	public function build()
	{
		$this->actors = [];
		foreach ($this->data->frames as $i => $frame) {
			foreach ($frame->replications as $replication) {
				if ($replication->state == 'opening') {
					$actor = $this->findActor($replication->actor_id);
					if (!$actor) {
						$this->actors[] = new Actor(
							$replication->actor_id,
							$replication->class_name,
							$i
						);
					}
				}
				elseif ($replication->state == 'existing') {
					$actor = $this->findActor($replication->actor_id);
					if (!$actor) {
						continue;
					}
					if (property_exists($replication->properties, 'Engine.Pawn:PlayerReplicationInfo')) {
						$parent = $this->findActor($replication->properties->{'Engine.Pawn:PlayerReplicationInfo'}->contents[1]);
						if ($parent) {
							$actor->parent = $parent;
						}
					}
					if (property_exists($replication->properties, 'TAGame.RBActor_TA:ReplicatedRBState')) {
						$actor->locations[] = new Location(
							new Point($replication->properties->{'TAGame.RBActor_TA:ReplicatedRBState'}->contents[1]),
							$frame->time
						);
					}
					if (property_exists($replication->properties, 'Engine.PlayerReplicationInfo:Team')) {
						$actor->setProp('team', $replication->properties->{'Engine.PlayerReplicationInfo:Team'}->contents[1]);
					}
				}
				elseif ($replication->state == 'closing') {
					$actor = $this->findActor($replication->actor_id);
					$actor->close_frame = $i;
				}
			}
		}
	}

	/**
	 * @param int $netstream_id
	 * @return Actor|null
	 */
	public function findActor($netstream_id)
	{
		for ($i = sizeof($this->actors) - 1; $i >= 0; $i--) {
			$actor = $this->actors[$i];
			if ($actor->netstream_id == $netstream_id && !$actor->close_frame) {
				return $actor;
			}
		}
		return null;
	}

	/**
	 * @return Box
	 */
	public function getBoundingBox()
	{
		$box = null;
		foreach ($this->data->frames as $frame) {
			foreach ($frame->replications as $replication) {
				if (!property_exists($replication->properties, 'TAGame.RBActor_TA:ReplicatedRBState')) {
					continue;
				}
				$point = new Point($replication->properties->{'TAGame.RBActor_TA:ReplicatedRBState'}->contents[1]);
				if (!$box) {
					$box = new Box(clone $point, clone $point);
				}
				$box->expandTo($point);
			}
		}
		return $box;
	}

	public function drawField()
	{
		$box = $this->getBoundingBox();
		$field = new FieldImage(600, $box);
		foreach ($this->actors as $actor) {
			if ($actor->class == 'TAGame.Ball_TA') {
				$field->drawActor($actor);
			}
			if ($actor->class == 'TAGame.Car_TA') {
				$color = null;
				switch ($actor->parent->getProp('team')) {
					case 5:
						$color = imagecolorallocate($field->canvas, 255, 0, 0);
						break;
					case 6:
						$color = imagecolorallocate($field->canvas, 0, 0, 255);
						break;
				}
				$field->drawActor($actor, $color);
			}
		}
		$field->output('field.png');
	}
}
