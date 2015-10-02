<?php namespace Exolnet\Cache;

use Illuminate\Support\Facades\Facade;

class SingleRequestCacheFacade extends Facade
{

	protected static function getFacadeAccessor()
	{
		return 'singleRequestCache';
	}
}
