<?php namespace Exolnet\Test;
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

use Config;
use Exolnet\Test\DatabaseMigrators\DatabaseMigrator;
use Exolnet\Test\DatabaseMigrators\SQLiteDatabaseMigrator;

class DatabaseMigratorFactory
{
	/**
	 * @return DatabaseMigrator|SQLiteDatabaseMigrator
	 */
	public function create()
	{
		if ($this->isSQLite() && $this->getSQLiteFile()) {
			return new SQLiteDatabaseMigrator($this->getSQLiteFile());
		} else {
			return new DatabaseMigrator();
		}
	}

	/**
	 * @return bool
	 */
	protected function isSQLite()
	{
		return strcasecmp(array_get($this->getDefaultConnectionConfiguration(), 'driver', ''), 'sqlite') === 0;
	}

	/**
	 * @return mixed|null
	 */
	protected function getSQLiteFile()
	{
		$file = array_get($this->getDefaultConnectionConfiguration(), 'database');
		if ($file === ':memory:') {
			return null;
		}
		return $file;
	}

	protected function getDefaultConnectionConfiguration()
	{
		$default = Config::get('database.default');
		return Config::get('database.connections.'.$default, []);
	}
}