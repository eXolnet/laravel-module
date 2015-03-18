<?php namespace Exolnet\Log\Processor;

use App;
use Config;

class LaravelProcessor
{
	public function __invoke(array $record)
	{
		$record['extra']['app'] = Config::get('log.app');
		$record['extra']['env'] = App::environment();

		return $record;
	}
}
