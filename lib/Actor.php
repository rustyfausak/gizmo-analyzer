<?php

namespace Gizmo;

class Actor
{
	public static $_id = 0;

	/**
	 * @param int $netstream_id
	 * @param string $class
	 * @param int $open_frame
	 */
	public function __construct($netstream_id, $class, $open_frame)
	{
		$this->id = self::$_id;
		self::$_id++;
		$this->netstream_id = $netstream_id;
		$this->class = $class;
		$this->open_frame = $open_frame;
		$this->close_frame = null;
		$this->parent = null;
		$this->locations = [];
		$this->properties = [];
	}

	public function setProp($name, $value)
	{
		$this->properties[$name] = $value;
	}

	public function getProp($name, $default = null)
	{
		if (array_key_exists($name, $this->properties)) {
			return $this->properties[$name];
		}
		return $default;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return '#' . $this->id . ' ' . $this->netstream_id . ' ' . $this->class  . ' (' . $this->open_frame . ' -> ' . $this->close_frame . ')' . ($this->parent ? ' child of ' . $this->parent->id : '') . ' - ' . sizeof($this->locations);
	}
}
