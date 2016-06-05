<?php

namespace Gizmo\Properties;

abstract class Property
{
	/* @var string */
	public $name;
	/* @var mixed */
	public $data;

	/**
	 * @param string $name
	 * @param mixed $data
	 */
	public function __construct($name, $data = null)
	{
		$this->name = $name;
		$this->data = [
			'unknown' => $data
		];
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		$str = $this->name . ' - ' . get_class($this) . "\n";
		foreach ($this->data as $k => $v) {
			if ($k == 'unknown') {
				$str .= '??? => ' . print_r($v, true) . "\n";
			}
			elseif (is_array($v)) {
				$str .= $k . ' => ' . implode(',', $v);
			}
			else {
				$str .= $k . ' => ' . $v . "\n";
			}
		}
		return $str;
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @param mixed $data
	 * @return Property
	 */
	public static function create($name, $type, $data)
	{
		$class = __NAMESPACE__ . '\\' . substr($type, 1) . 'Property';
		if (!class_exists($class)) {
			throw new \Exception("Invalid property type '{$type}' ({$class}).");
		}
		return new $class($name, $data);
	}
}
