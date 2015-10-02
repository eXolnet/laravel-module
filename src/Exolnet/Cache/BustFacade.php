<?php namespace Exolnet\Cache;

use Illuminate\Support\Facades\Facade;

class BustFacade extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'bust';
	}
}
