<?php namespace Exolnet\Test;

use Config;
use Exolnet\Test\DatabaseMigrators\DatabaseMigrator;
use Exolnet\Test\DatabaseMigrators\MySQLDatabaseMigrator;
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
		} elseif ($this->isMySQL()) {
			return app(MySQLDatabaseMigrator::class, ['identifier' => $this->getDatabase()]);
		} else {
			return new DatabaseMigrator();
		}
	}

	/**
	 * @return bool
	 */
	protected function isSQLite()
	{
		return strcasecmp($this->getDriver(), 'sqlite') === 0;
	}

	/**
	 * @return bool
	 */
	protected function isMySQL()
	{
		return strcasecmp($this->getDriver(), 'mysql') === 0;
	}

	/**
	 * @return mixed|null
	 */
	protected function getSQLiteFile()
	{
		$file = $this->getDatabase();
		if ($file === ':memory:') {
			return null;
		}
		return $file;
	}

	/**
	 * @return array
	 */
	protected function getDefaultConnectionConfiguration()
	{
		$default = Config::get('database.default');
		return Config::get('database.connections.' . $default, []);
	}

	/**
	 * @return string|null
	 */
	protected function getDatabase()
	{
		return array_get($this->getDefaultConnectionConfiguration(), 'database');
	}

	/**
	 * @return string|null
	 */
	protected function getDriver()
	{
		return array_get($this->getDefaultConnectionConfiguration(), 'driver');
	}
}
