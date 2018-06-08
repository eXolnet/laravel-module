<?php

namespace Exolnet\Test\DatabaseMigrators;

use Exolnet\Database\Dumper\MySqlDumper;

class MySQLDatabaseMigrator extends DumpBasedMigrator
{
	/**
	 * @var \Exolnet\Database\Dumper\MySqlDumper
	 */
	protected $mySqlDumper;

	public function __construct(string $identifier, MySqlDumper $mySqlDumper)
	{
		parent::__construct($identifier . '.sql');
		$this->mySqlDumper = $mySqlDumper;
	}

	protected function save(): void
	{
		$this->mySqlDumper->export($this->cloneFile);
	}

	protected function restore(): void
	{
		$this->mySqlDumper->import($this->cloneFile);
	}
}
