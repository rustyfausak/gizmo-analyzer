<?php

namespace Gizmo\Game;

trait RigidBody
{
	/**
	 * @param Hitbox $hitbox
	 */
	public function initRigidBody($hitbox = null)
	{
		$this->distance = 0;
		$this->location = null;
		$this->rotation = null;
		$this->actor_id = null;
		$this->hitbox = $hitbox;
	}

	/**
	 * @param Hitbox $hitbox
	 */
	public function setHitbox($hitbox)
	{
		$this->hitbox = $hitbox;
	}

	/**
	 * @param $other
	 */
	public function intersects($other)
	{
		return $this->hitbox && $other->hitbox && $this->hitbox->intersects($other->hitbox);
	}

	/**
	 */
	public function __toString()
	{
		return get_class($this) . ' (' . $this->actor_id . ') ' . $this->location . ' ' . $this->rotation . "\tdistance => " . $this->distance;
	}

	/**
	 * @param Frame $frame
	 * @param Actor $actor
	 */
	public function update(Frame $frame, Actor $actor)
	{
		if ($this->actor_id === null) {
			// needs init
			$this->location = $actor->location;
			$this->rotation = $actor->rotation;
		}
		$rigid_body_property = $actor->getProperty('TAGame.RBActor_TA:ReplicatedRBState');
		if (!$rigid_body_property) {
			// no rigid body prop, nothing to do
			$this->actor_id = $actor->id;
			if ($this->hitbox) {
				$this->hitbox->update($this->location, $this->rotation);
			}
			return;
		}
		if ($this->actor_id !== null && $actor->id == $this->actor_id && $this->location) {
			// last actor was the same and we have a prev location, so generate diff
			$this->distance += $this->location->distanceTo($rigid_body_property->location);
		}
		$this->location = $rigid_body_property->location;
		$this->rotation = $rigid_body_property->rotation;
		$this->actor_id = $actor->id;
		if ($this->hitbox) {
			$this->hitbox->update($this->location, $this->rotation);
		}
	}
}
