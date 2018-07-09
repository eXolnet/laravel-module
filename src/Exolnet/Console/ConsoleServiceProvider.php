<?php namespace Exolnet\Console;

use Exolnet\Console\Commands\MakeModels;
use Exolnet\Console\Commands\MakeModelsAccessors;
use Exolnet\Console\Commands\QueueSetup;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->commands([
			MakeModels::class,
			MakeModelsAccessors::class,
			QueueSetup::class,
		]);
	}
}
