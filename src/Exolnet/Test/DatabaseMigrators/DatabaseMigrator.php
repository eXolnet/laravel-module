<?php namespace Exolnet\Test\DatabaseMigrators;

use Artisan;
use Schema;

class DatabaseMigrator
{
	/**
	 *
	 */
	public function __construct()
	{
	}

	public function run()
	{
		if (Schema::hasTable('migrations')) {
			Artisan::call('migrate:reset');
		}
		Artisan::call('migrate');
		if (file_exists(base_path('database/seeds/TestSeeder.php'))) {
			Artisan::call('db:seed', ['--class' => 'TestSeeder']);
		}
	}
}
