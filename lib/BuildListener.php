<?php

namespace Gizmo;

abstract class BuildListener implements \JsonStreamingParser\Listener
{
	/* @var int */
	public $level;
	/* @var array */
	public $stack;
	/* @var array */
	public $keys;
	/* @var string */
	public $build_callback;
	/* @var int */
	public $build_depth;

	/**
	 */
	abstract public function start();

	/**
	 * @param string $key
	 */
	abstract public function readKey($key);

	/**
	 * @param mixed $value
	 */
	abstract public function readValue($value);

	/**
	 * @param string $type
	 */
	abstract public function eventStartComplex($type);

	/**
	 */
	public function __construct() {}

	/**
	 */
	public function startDocument()
	{
		$this->start();
		$this->level = 0;
		$this->stack = [];
		$this->keys = [];
		$this->build_callback = null;
		$this->build_depth = 0;
	}

	/**
	 */
	public function endDocument() {}

	/**
	 * @param string $type
	 */
	public function startComplex($type)
	{
		$this->level++;
		$this->eventStartComplex($type);
		if ($this->build_callback) {
			$this->build_depth++;
			$this->startComplexValue($type);
		}
	}

	/**
	 */
	public function endComplex()
	{
		$this->level--;
		if ($this->build_callback) {
			$this->build_depth--;
			$this->endComplexValue();
		}
	}

	/**
	 */
	public function startObject()
	{
		$this->startComplex('object');
	}

	/**
	 */
	public function endObject()
	{
		$this->endComplex();
	}

	/**
	 */
	public function startArray()
	{
		$this->startComplex('array');
	}

	/**
	 */
	public function endArray()
	{
		$this->endComplex();
	}

	/**
	 * @param string $type
	 */
	protected function startComplexValue($type)
	{
		$item = ['type' => $type, 'value' => []];
		$this->stack[] = $item;
	}

	protected function endComplexValue()
	{
		$obj = array_pop($this->stack);
		if (empty($this->stack)) {
			$this->{$this->build_callback}($obj['value']);
			$this->build_callback = null;
		}
		else {
			$this->insertValue($obj['value']);
		}
	}

	/**
	 * @param mixed $value
	 */
    protected function insertValue($value)
	{
		if (!$this->build_depth) {
			$this->{$this->build_callback}($value);
			$this->build_callback = null;
			return;
		}
		$item = array_pop($this->stack);
		if ($item['type'] === 'object') {
			$item['value'][array_pop($this->keys)] = $value;
		}
		else {
			$item['value'][] = $value;
		}
		$this->stack[] = $item;
	}

	/**
	 * @param string $key
	 */
	public function key($key)
	{
		if ($this->build_callback) {
			$this->keys[] = $key;
		}
		else {
			$this->readKey($key);
		}
	}

	/**
	 * @param mixed $value
	 */
	public function value($value)
	{
		if ($this->build_callback) {
			$this->insertValue($value);
			return;
		}
		else {
			$this->readValue($value);
		}
	}

	/**
	 * @param string $whitespace
	 */
	public function whitespace($whitespace)
	{
	}
}
