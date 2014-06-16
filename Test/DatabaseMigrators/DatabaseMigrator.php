<?php namespace Exolnet\Test\DatabaseMigrators;

use Artisan;
use Schema;

class DatabaseMigrator
{
	public function __construct(){
		
	}
	
	public function run()
	{
		if (Schema::hasTable('migrations')) {
			Artisan::call('migrate:reset');
		}
		Artisan::call('migrate');
		Artisan::call('db:seed');
	}
} 