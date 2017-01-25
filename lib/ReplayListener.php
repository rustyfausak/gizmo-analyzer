<?php

namespace Gizmo;

use \Gizmo\Game\Actor;
use \Gizmo\Game\Frame;
use \Gizmo\Game\Game;
use \Gizmo\Game\Property;
use \Gizmo\Helper;
use \Gizmo\Space\Vector;
use \Gizmo\Space\Rotation;

class ReplayListener extends BuildListener
{
	const STATE_UNKNOWN = -1;
	const STATE_START_DOCUMENT = 0;
	const STATE_IN_META = 1;
	const STATE_IN_FRAMES = 3;

	/* @var Game */
	public $game;
	/* @var int */
	public $state;
	/* @var int */
	public $frame_number;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 */
	public function start()
	{
		$this->game = null;
		$this->state = self::STATE_START_DOCUMENT;
		$this->frame_number = 0;
	}

	/**
	 * @param string $key
	 */
	public function readKey($key)
	{
		switch ($this->level) {
			case 1:
				switch ($key) {
					case 'meta':
						$this->state = self::STATE_IN_META;
						$this->build_callback = 'processMeta';
						break;
					case 'frames':
						$this->state = self::STATE_IN_FRAMES;
						break;
					default:
						$this->state = self::STATE_UNKNOWN;
						break;
				}
				break;
		}
	}

	/**
	 * @param mixed $value
	 */
	public function readValue($value) {}

	/**
	 * @param string $type
	 */
	public function eventStartComplex($type)
	{
		if ($this->level == 3 && $this->state == self::STATE_IN_FRAMES) {
			$this->build_callback = 'processFrame';
		}
	}

	/**
	 * @param array $meta
	 */
	public function processMeta($meta)
	{
		$this->game = new Game($meta);
	}

	/**
	 * @param array $frame_data
	 */
	public function processFrame($frame_data)
	{
		$this->frame_number++;
		$frame = new Frame(
			$this->frame_number,
			$frame_data['time'],
			$frame_data['delta']
		);
		if (array_key_exists('replications', $frame_data)) {
			foreach ($frame_data['replications'] as $replication) {
				$this->processReplication($frame, $replication);
			}
		}
		$this->game->processFrame($frame, $this->actors);
	}

	/**
	 * @param Frame $frame
	 * @param array $replication
	 */
	public function processReplication($frame, $replication)
	{
		$actor_id = $replication['actor_id'];
		switch ($replication['state']) {

			// New actor
			case 'opening':
				$location = null;
				$rotation = null;
				if ($arr = Helper::getElem($replication, ['initialization', 'location'])) {
					$location = Vector::withArr($arr);
				}
				if ($arr = Helper::getElem($replication, ['initialization', 'rotation'])) {
					$rotation = Rotation::with255Arr($arr);
				}
				$actor = new Actor(
					$actor_id,
					$replication['class_name'],
					$replication['object_name'],
					$location,
					$rotation
				);
				$this->actors[$actor_id] = $actor;
				break;

			// Existing actor
			case 'existing':
				if (!array_key_exists($actor_id, $this->actors)) {
					throw new \Exception("Could not find actor ID '{$actor_id}'.");
				}
				foreach ($replication['properties'] as $name => $arr) {
					$type = substr($arr['tag'], 1);
					$this->actors[$actor_id]->setProperty($name, $type, $arr['contents']);
				}
				break;

			// Close actor
			case 'closing':
				if (!array_key_exists($actor_id, $this->actors)) {
					throw new \Exception("Could not find actor ID '{$actor_id}'.");
				}
				unset($this->actors[$actor_id]);
				break;

		}
	}
}
