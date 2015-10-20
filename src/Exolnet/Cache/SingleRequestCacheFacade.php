<?php namespace Exolnet\Cache;

use Illuminate\Support\Facades\Facade;

class SingleRequestCacheFacade extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'singleRequestCache';
	}
}
