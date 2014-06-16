<?php namespace Exolnet\Core;

class EnvironmentDetector
{
	public static function detect($environments)
	{
		// TODO: Do not allow $is_behat = true in production environment
		$is_behat = self::is_remote_test();
		if ($is_behat) {
			return function() { return 'test'; };
		}

		return $environments;
	}

	public static function is_remote_test()
	{
		return array_get($_COOKIE, 'test-env') === 'true';
	}
}