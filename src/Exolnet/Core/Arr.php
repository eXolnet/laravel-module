<?php namespace Exolnet\Core;

use Illuminate\Support\Arr as LaravelArr;

class Arr extends LaravelArr
{
	/**
	 * @param array $array
	 * @param string $value
	 * @param string|null $key
	 * @return array
	 */
	public static function pluck($array, $value, $key = null)
	{
		$results = [];

		foreach ($array as $item) {
			$itemValue = is_object($item) ? object_get($item, $value) : array_get($item, $value);

			// If the key is "null", we will just append the value to the array and keep
			// looping. Otherwise we will key the array using the value of the key we
			// received from the developer. Then we'll return the final array form.
			if ($key === null) {
				$results[] = $itemValue;
			} else {
				$itemKey = is_object($item) ? object_get($item, $key) : array_get($item, $key);

				$results[$itemKey] = $itemValue;
			}
		}

		return $results;
	}

	/**
	 * Replace empty strings to NULLs values.
	 *
	 * @param array $items
	 * @return array
	 */
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

	/**
	 * Remove one or multiple keys from an array using the dot notation.
	 *
	 * @param array $array
	 * @param $keys
	 */
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

	/**
	 * Load values from a dictionary in an array. Values are linked with a lookup key and a
	 * target key is set in the array with the value found in the dictionary.
	 *
	 * @param \ArrayAccess|array $array
	 * @param \ArrayAccess|array $dictionary
	 * @param string $lookupKey
	 * @param string $targetKey
	 * @param mixed $defaultValue
	 */
	public static function inject(&$array, $dictionary, $lookupKey, $targetKey, $defaultValue = null)
	{
		foreach ($array as &$item) {
			$value = self::get($dictionary, self::get($item, $lookupKey), $defaultValue);

			self::set($item, $targetKey, $value);
		}
	}

	/**
	 * @param \ArrayAccess|array $array
	 * @param \ArrayAccess|array $dictionary
	 * @param string $lookupKey
	 */
	public static function injectAll(&$array, $dictionary, $lookupKey)
	{
		foreach ($array as &$item) {
			$values = self::get($dictionary, self::get($item, $lookupKey), []);

			foreach ($values as $key => $value) {
				self::set($item, $key, $value);
			}
		}
	}
}
