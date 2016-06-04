<?php

namespace Gizmo;

class Listener implements \JsonStreamingParser\Listener
{
	const STATE_UNKNOWN = -1;
	const STATE_START_DOCUMENT = 0;
	const STATE_IN_META = 1;
	const STATE_IN_META_PROPERTIES = 2;
	const STATE_IN_FRAMES = 3;
	const STATE_IN_REPLICATIONS = 4;

	public $state;
	public $level;
	public $key;
	public $game;

	public function __construct() {}

	/**
	 * @param int $state
	 * @return bool
	 */
	public function isState($state)
	{
		return $this->state == $state;
	}

	public function startDocument()
	{
		$this->state = self::STATE_START_DOCUMENT;
		$this->level = 0;
		$this->key = null;
		$this->game = new Models\Game();
	}

	public function endDocument() {}

	public function startObject()
	{
		$this->level++;
	}

	public function endObject()
	{
		$this->level--;
	}

	public function startArray()
	{
		$this->startObject();
	}

	public function endArray()
	{
		$this->endObject();
	}

	/**
	 * @param string $key
	 */
	public function key($key)
	{
		$this->key = $key;
		switch ($this->level) {
			case 1:
				switch ($key) {
					case 'meta':
						$this->state = self::STATE_IN_META;
						break;
					case 'frames':
						$this->state = self::STATE_IN_FRAMES;
						$this->game->save();
						print_r($this->game);
						exit;
						break;
					default:
						$this->state = self::STATE_UNKNOWN;
						break;
				}
				break;
			case 2:
				switch ($this->state) {
					case self::STATE_IN_META:
						switch ($key) {
							case 'properties':
								$this->state = self::STATE_IN_META_PROPERTIES;
								break;
							default:
								$this->state = self::STATE_IN_META;
								break;
						}
						break;
					case self::STATE_IN_FRAMES:
						switch ($key) {
							case 'replications':
								$this->state = self::STATE_IN_REPLICATIONS;
								break;
							default:
								$this->state = self::STATE_UNKNOWN;
								break;
						}
						break;
				}
				break;
		}
	}

	/**
	 * Value may be a string, integer, boolean, etc.
	 * @param mixed $value
	 */
	public function value($value)
	{
		switch ($this->state) {
			case self::STATE_IN_META:
				switch ($this->level) {
					case 2:
						switch ($this->key) {
							case 'version1':
								$this->game->setVersion($value);
								break;
							case 'version2':
								$this->game->setVersion($this->game->getVersion() . '.' . $value);
								break;

						}
						break;
				}
				break;
			case self::STATE_IN_META_PROPERTIES:
				switch ($this->key) {
					case 'Id':
						$this->game->setGameId($value);
						break;
					case 'BuildID':
						$this->game->setBuildId($value);
						break;
					case 'Date':
						list($date, $time) = explode(':', $value);
						list($hour, $min) = explode('-', $time);
						$date = date("Y-m-d H:i:s", strtotime($date . ' ' . $hour . ':' . $min));
						$this->game->setDate($date);
						break;
					case 'BuildVersion':
					case 'GameVersion':
					case 'MapName':
					case 'MatchType':
					case 'PlayerName':
					case 'PrimaryPlayerTeam':
					case 'ReplayVersion':
					case 'Team0Score':
					case 'Team1Score':
					case 'TeamSize':
						$this->game->setByName(ucfirst($this->key), $value);
						break;
				}
				break;
		}
	}

	/**
	 * We don't care about whitespace.
	 *
	 * @param string $whitespace
	 */
	public function whitespace($whitespace)
	{
	}
}
