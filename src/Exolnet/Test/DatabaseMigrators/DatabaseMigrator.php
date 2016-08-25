<?php namespace Exolnet\Test\DatabaseMigrators;

use Artisan;
use Schema;

class DatabaseMigrator
{
	/**
	 * @var string
	 */
	protected $filesPath = 'database';
	/**
	 * @var string
	 */
	protected $seedClass = 'TestSeeder';
	/**
	 * @var null|string
	 */
	protected $connection;

	/**
	 * @param string|null $connection
	 */
	public function __construct($connection = null)
	{
		$this->connection = $connection;
	}

	public function run()
	{
		$this->reset();
		$this->migrate();
		$this->seed();
	}

	/**
	 * @param string $filesPath
	 * @return $this
	 */
	public function setFilesPath($filesPath)
	{
		$this->filesPath = $filesPath;

		return $this;
	}

	/**
	 * @param string $seedClass
	 * @return $this
	 */
	public function setSeedClass($seedClass)
	{
		$this->seedClass = $seedClass;

		return $this;
	}

	protected function reset()
	{
		if (Schema::hasTable('migrations')) {
			Artisan::call('migrate:reset', [
				'--database' => $this->connection,
			]);
		}
	}

	protected function migrate()
	{
		Artisan::call('migrate', [
			'--database' => $this->connection,
			'--path' => $this->filesPath.'/migrations'
		]);
	}

	protected function seed()
	{
		if (file_exists(base_path($this->filesPath.'/seeds/'.$this->seedClass.'.php'))) {
			Artisan::call('db:seed', [
				'--class' => $this->seedClass,
				'--database' => $this->connection,
			]);
		}
	}
}
