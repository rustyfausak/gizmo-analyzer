<?php

namespace Gizmo\Game;

class Actor
{
	/**
	 * @param int $id
	 * @param string $class_name
	 * @param string $object_name
	 * @param Vector|null $location
	 * @param Vector|null $rotation
	 */
	public function __construct($id, $class_name, $object_name, $location = null, $rotation = null)
	{
		$this->id = $id;
		$this->class_name = $class_name;
		$this->object_name = $object_name;
		$this->location = $location;
		$this->rotation = $rotation;
		$this->properties = [];
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @param mixed $data
	 */
	public function setProperty($name, $type, $data)
	{
		if (!array_key_exists($name, $this->properties)) {
			// Create
			$this->properties[$name] = new Property($name, $type, $data);
		}
		else {
			// Update
			$this->properties[$name]->update($data);
		}
	}

	/**
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getProperty($name, $default = null)
	{
		if (array_key_exists($name, $this->properties)) {
			return $this->properties[$name];
		}
		return $default;
	}
}
