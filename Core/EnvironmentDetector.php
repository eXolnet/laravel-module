<?php namespace Exolnet\Core;
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

class EnvironmentDetector
{
	private static $environments = [
		'development.exolnet.com' => 'local',
		'staging.exolnet.com'     => 'staging',
		'testing.exolnet.com'     => 'staging',
	];

	/**
	 * @param $environments
	 * @return callable
	 */
	public static function detect($environments)
	{
		// TODO: Do not allow $is_behat = true in production environment
		$is_behat = self::is_remote_test();
		if ($is_behat) {
			return function() { return 'test'; };
		}

		if (isset($_SERVER['HTTP_HOST']))
		{
			$host = $_SERVER['HTTP_HOST'];

			if (array_key_exists($host, self::$environments)) {
				return function() use ($host) { return self::$environments[$host]; };
			}
		}

		return $environments;
	}

	/**
	 * Add environements to host detector.
	 *
	 * @param array $environments
	 */
	public static function addEnvironments(array $environments)
	{
		static::$environments = $environments + static::$environments;
	}

	/**
	 * @return bool
	 */
	public static function is_remote_test()
	{
		return array_get($_COOKIE, 'test-env') === 'true';
	}
}
