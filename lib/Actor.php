<?php

namespace Gizmo;

class Actor
{
	/* @var int */
	public $id;
	/* @var string */
	public $class_name;
	/* @var string */
	public $object_name;
	/* @var Vector */
	public $location;
	/* @var Rotation */
	public $rotation;
	/* @var array of Property */
	public $properties;

	/**
	 * @param int $id
	 * @param string $class_name
	 * @param string $object_name
	 * @param Vector|null $location
	 * @param Rotation|null $rotation
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
	 * @return string
	 */
	public function __toString()
	{
		$str = __CLASS__ . ' #' . $this->id . ' - ' . $this->class_name . ' - ' . $this->object_name . "\n"
			. $this->location . "\n"
			. $this->rotation . "\n"
			. "Properties:\n";
		foreach ($this->properties as $property) {
			$str .= "\t" . $property . "\n";
		}
		return $str;
	}

	/**
	 * @param array $replication
	 */
	public function update($replication)
	{
		$properties = Helper::getElem($replication, 'properties');
		if (!$properties || empty($properties)) {
			return;
		}
		foreach ($properties as $name => $arr) {
			$type = Helper::getElem($arr, 'tag');
			$data = Helper::getElem($arr, 'contents');
			$this->properties[$name] = Properties\Property::create($name, $type, $data);
		}
	}

	/**
	 */
	public function close() {}

	/**
	 * @param array $replication
	 * @return Actor
	 */
	public static function create($replication)
	{
		$location = null;
		$rotation = null;
		if ($arr = Helper::getElem($replication, ['initialization', 'location'])) {
			$location = Space\Vector::withArr($arr);
		}
		if ($arr = Helper::getElem($replication, ['initialization', 'rotation'])) {
			$rotation = Space\Rotation::withArr($arr);
		}
		return new self(
			Helper::getElem($replication, 'actor_id'),
			Helper::getElem($replication, 'class_name'),
			Helper::getElem($replication, 'object_name'),
			$location,
			$rotation
		);
	}
}
