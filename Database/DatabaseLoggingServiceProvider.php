<?php namespace Exolnet\Database;
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
 * @subpackage Database
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class DatabaseLoggingServiceProvider extends ServiceProvider
{
	public function register()
	{
		$log = new Logger('db');
		$log->pushHandler(new StreamHandler(storage_path().'/logs/laravel-db.log'));

		DB::listen(function($sql, $bindings, $time) use ($log) {
			$sql = str_replace(['%', '?'], ['%%', '%s'], $sql);
			$full_sql = vsprintf($sql, $bindings);
			//echo PHP_EOL.'- BEGIN QUERY -'.PHP_EOL.$full_sql.PHP_EOL.'- END QUERY -'.PHP_EOL;
			$log->addInfo($full_sql);
		});
	}
}