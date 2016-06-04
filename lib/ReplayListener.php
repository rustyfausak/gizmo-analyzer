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
			'version'        => self::getElem($meta, 'version1') . '.' . self::getElem($meta, 'version2'),
			'build_id'       => self::getElem($meta, ['properties', 'BuildID']),
			'build_version'  => self::getElem($meta, ['properties', 'BuildVersion']),
			'date'           => self::fromDateStr(self::getElem($meta, ['properties', 'Date'])),
			'game_id'        => self::getElem($meta, ['properties', 'Id']),
			'game_version'   => self::getElem($meta, ['properties', 'GameVersion']),
			'map_name'       => self::getElem($meta, ['properties', 'MapName']),
			'match_type'     => self::getElem($meta, ['properties', 'MatchType']),
			'player_name'    => self::getElem($meta, ['properties', 'PlayerName']),
			'primary_player_team' => self::getElem($meta, ['properties', 'PrimaryPlayerTeam']),
			'replay_version' => self::getElem($meta, ['properties', 'ReplayVersion']),
			'team0_score'    => self::getElem($meta, ['properties', 'Team0Score']),
			'team1_score'    => self::getElem($meta, ['properties', 'Team1Score']),
			'team_size'      => self::getElem($meta, ['properties', 'TeamSize'])
		], Models\Map\GameTableMap::TYPE_FIELDNAME);
		$this->game->save();
	}

	/**
	 * @param mixed $frame
	 */
	public function processFrame($frame)
	{
		$this->frame_number++;
		$time = self::getElem($frame, 'time');
		$delta = self::getElem($frame, 'delta');
		if (array_key_exists('replications', $frame)) {
			foreach ($frame['replications'] as $replication) {
				$this->processReplication($replication);
			}
		}
		exit;
	}

	/**
	 * @param mixed $replication
	 */
	public function processReplication($replication)
	{
		print_r($replication);
		exit;
	}

	/**
	 * @param array $arr
	 * @param string|array $keys
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getElem($arr, $keys, $default = null)
	{
		if (!is_array($keys)) {
			$keys = [$keys];
		}
		$cur = $arr;
		foreach ($keys as $key) {
			if (!array_key_exists($key, $cur)) {
				return $default;
			}
			$cur = $cur[$key];
		}
		return $cur;
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
