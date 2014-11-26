<?php namespace Exolnet\Test\DatabaseMigrators;
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
 * @subpackage Test
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use Artisan;
use Schema;

class DatabaseMigrator
{
	/**
	 *
	 */
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