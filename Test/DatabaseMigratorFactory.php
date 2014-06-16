<?php namespace Exolnet\Test;

use Config;
use Exolnet\Test\DatabaseMigrators\DatabaseMigrator;
use Exolnet\Test\DatabaseMigrators\SQLiteDatabaseMigrator;

class DatabaseMigratorFactory
{
	public function create()
	{
		if ($this->isSQLite() && $this->getSQLiteFile()) {
			return new SQLiteDatabaseMigrator($this->getSQLiteFile());
		} else {
			return new DatabaseMigrator();
		}
	}

	protected function isSQLite()
	{
		return strcasecmp(array_get($this->getDefaultConnectionConfiguration(), 'driver', ''), 'sqlite') === 0;
	}

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