<?php namespace Exolnet\Core;

class Arr
{
	public static function pluck($array, $value, $key = null)
	{
		$results = [];

		foreach ($array as $item) {
			$itemValue = is_object($item) ? object_get($item, $value) : array_get($item, $value);

			// If the key is "null", we will just append the value to the array and keep
			// looping. Otherwise we will key the array using the value of the key we
			// received from the developer. Then we'll return the final array form.
			if (is_null($key)) {
				$results[] = $itemValue;
			} else {
				$itemKey = is_object($item) ? object_get($item, $key) : array_get($item, $key);

				$results[$itemKey] = $itemValue;
			}
		}

		return $results;
	}

	public static function mapNullOnEmpty(array $items)
	{
		return array_map(function ($item) {
			if (is_array($item)) {
				return static::mapNullOnEmpty($item);
			}

			if (is_string($item) && $item === '') {
				return null;
			}

			return $item;
		}, $items);
	}

	public static function forget(&$array, $keys)
	{
		$original =& $array;

		foreach ((array)$keys as $key) {
			$parts = explode('.', $key);

			$shouldUnset = true;
			while (count($parts) > 1) {
				$part = array_shift($parts);

				if (isset($array[$part]) && is_array($array[$part])) {
					$array =& $array[$part];
				} elseif ($part === '*') {
					$subKey = implode('.', $parts);
					foreach ($array as $key => &$value) {
						if (is_array($value)) {
							static::forget($value, $subKey);
						}
					}
					$shouldUnset = false;
					break;
				}
			}

			// TODO-TR: Add support for x.*
			if ($shouldUnset) {
				unset($array[array_shift($parts)]);
			}

			// reset to initial array
			$array =& $original;
		}
	}
}
