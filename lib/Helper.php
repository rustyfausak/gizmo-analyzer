<?php

namespace Gizmo;

class Helper
{
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
