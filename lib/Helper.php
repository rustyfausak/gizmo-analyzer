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
}
