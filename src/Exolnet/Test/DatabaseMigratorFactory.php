<?php namespace Exolnet\Test;

use Config;
use Exolnet\Test\DatabaseMigrators\DatabaseMigrator;
use Exolnet\Test\DatabaseMigrators\SQLiteDatabaseMigrator;

class DatabaseMigratorFactory
{
	/**
	 * @param string|null $connection
	 * @return \Exolnet\Test\DatabaseMigrators\DatabaseMigrator|\Exolnet\Test\DatabaseMigrators\SQLiteDatabaseMigrator
	 */
	public function create($connection = null)
	{
		if ($this->isSQLite($connection) && $this->getSQLiteFile($connection)) {
			return new SQLiteDatabaseMigrator($this->getSQLiteFile($connection), $connection);
		} else {
			return new DatabaseMigrator($connection);
		}
	}

	/**
	 * @param string $connection
	 * @return bool
	 */
	protected function isSQLite($connection)
	{
		return strcasecmp(array_get($this->getConnectionConfiguration($connection), 'driver', ''), 'sqlite') === 0;
	}

	/**
	 * @return string|null
	 */
	protected function getSQLiteFile($connection)
	{
		$file = array_get($this->getConnectionConfiguration($connection), 'database');
		if ($file === ':memory:') {
			return null;
		}
		return $file;
	}

	/**
	 * @param string $connection
	 * @return array
	 */
	protected function getConnectionConfiguration($connection)
	{
		$connectionName = $connection ?: Config::get('database.default');
		return Config::get('database.connections.' . $connectionName, []);
	}
}
