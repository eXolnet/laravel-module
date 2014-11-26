<?php
/**
 * Copyright © 2014 eXolnet Inc. All rights reserved. (http://www.exolnet.com)
 *
 * This file contains copyrighted code that is the sole property of eXolnet Inc.
 * You may not use this file except with a written agreement.
 *
 * This code is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND,
 * EITHER EXPRESS OR IMPLIED, AND EXOLNET INC. HEREBY DISCLAIMS ALL SUCH
 * WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT.
 *
 * @package    Exolnet
 * @subpackage Core
 * @author     eXolnet Inc. <info@exolnet.com>
 */

namespace Exolnet\Core;

class Arr {
	public static function pluck($array, $value, $key = null)
	{
		$results = array();

		foreach ($array as $item)
		{
			$itemValue = is_object($item) ? object_get($item, $value) : array_get($item, $value);

			// If the key is "null", we will just append the value to the array and keep
			// looping. Otherwise we will key the array using the value of the key we
			// received from the developer. Then we'll return the final array form.
			if (is_null($key))
			{
				$results[] = $itemValue;
			}
			else
			{
				$itemKey = is_object($item) ? object_get($item, $key) : array_get($item, $key);

				$results[$itemKey] = $itemValue;
			}
		}

		return $results;
	}

	public static function mapNullOnEmpty(array $items)
	{
		return array_map(function($item) {
			if (is_array($item)) {
				return static::mapNullOnEmpty($item);
			}

			if (is_string($item) && $item === '') {
				return null;
			}

			return $item;
		}, $items);
	}
}
