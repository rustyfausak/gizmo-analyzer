<?php

namespace Gizmo;

class ReplayListener extends BuildListener
{
	const STATE_UNKNOWN = -1;
	const STATE_START_DOCUMENT = 0;
	const STATE_IN_META = 1;
	const STATE_IN_FRAMES = 3;

	/* @var Models\Game */
	public $game;
	/* @var array */
	public $actors;
	/* @var int */
	public $state;
	/* @var int */
	public $frame_number;

	/**
	 */
	public function start()
	{
		$this->game = new Models\Game();
		$this->actors = [];
		$this->state = self::STATE_START_DOCUMENT;
		$this->frame_number = -1;
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
	 * @param mixed $meta
	 */
	public function processMeta($meta)
	{
		$this->game->fromArray([
			'version'        => Helper::getElem($meta, 'version1') . '.' . Helper::getElem($meta, 'version2'),
			'build_id'       => Helper::getElem($meta, ['properties', 'BuildID']),
			'build_version'  => Helper::getElem($meta, ['properties', 'BuildVersion']),
			'date'           => self::fromDateStr(Helper::getElem($meta, ['properties', 'Date'])),
			'game_id'        => Helper::getElem($meta, ['properties', 'Id']),
			'game_version'   => Helper::getElem($meta, ['properties', 'GameVersion']),
			'map_name'       => Helper::getElem($meta, ['properties', 'MapName']),
			'match_type'     => Helper::getElem($meta, ['properties', 'MatchType']),
			'player_name'    => Helper::getElem($meta, ['properties', 'PlayerName']),
			'primary_player_team' => Helper::getElem($meta, ['properties', 'PrimaryPlayerTeam']),
			'replay_version' => Helper::getElem($meta, ['properties', 'ReplayVersion']),
			'team0_score'    => Helper::getElem($meta, ['properties', 'Team0Score']),
			'team1_score'    => Helper::getElem($meta, ['properties', 'Team1Score']),
			'team_size'      => Helper::getElem($meta, ['properties', 'TeamSize'])
		], Models\Map\GameTableMap::TYPE_FIELDNAME);
		$this->game->save();
	}

	/**
	 * @param mixed $frame
	 */
	public function processFrame($frame)
	{
		$this->frame_number++;
		print "FRAME #{$this->frame_number}\n";
		$time = Helper::getElem($frame, 'time');
		$delta = Helper::getElem($frame, 'delta');
		if (array_key_exists('replications', $frame)) {
			foreach ($frame['replications'] as $replication) {
				$this->processReplication($replication);
			}
			foreach ($this->actors as $actor) {
				if ($actor->class_name == 'TAGame.Car_TA') {
					print $actor . "\n";
				}
			}
		}
		fgets(STDIN);
	}

	/**
	 * @param mixed $replication
	 */
	public function processReplication($replication)
	{
		$actor_id = Helper::getElem($replication, 'actor_id');
		if ($actor_id === null) {
			throw new \Exception("No actor ID found in replication.");
		}
		switch (Helper::getElem($replication, 'state')) {
			case 'opening':
				$this->actors[$actor_id] = Actor::create($replication);
				break;
			case 'existing':
				if (!array_key_exists($actor_id, $this->actors)) {
					throw new \Exception("Could not find actor with ID '{$actor_id}'.");
				}
				$this->actors[$actor_id]->update($replication);
				break;
			case 'closing':
				if (!array_key_exists($actor_id, $this->actors)) {
					throw new \Exception("Could not find actor with ID '{$actor_id}'.");
				}
				$this->actors[$actor_id]->close();
				unset($this->actors[$actor_id]);
				break;
		}
	}

	/**
	 * @param string $str
	 * @return string|null
	 */
	public static function fromDateStr($str)
	{
		$parts = explode(':', $str);
		if (sizeof($parts) != 2) {
			return null;
		}
		$time_parts = explode('-', $parts[1]);
		if (sizeof($time_parts) != 2) {
			return null;
		}
		$time = strtotime($parts[0] . ' ' . $time_parts[0] . ':' . $time_parts[1]);
		if (!$time) {
			return null;
		}
		return date("Y-m-d H:i:s", $time);
	}
}
