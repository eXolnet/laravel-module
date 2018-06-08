<?php

namespace Exolnet\Database\Dumper;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class MySqlDumper
{
	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * @var \Symfony\Component\Process\ExecutableFinder
	 */
	protected $executableFinder;

	/**
	 * @var string|null
	 */
	protected $commandMysqlDump;

	/**
	 * @var string|null
	 */
	protected $commandMysql;

	public function __construct(Filesystem $filesystem, ExecutableFinder $executableFinder)
	{
		$this->filesystem = $filesystem;
		$this->executableFinder = $executableFinder;
	}

	public function export(string $file, bool $withCreateStatements = true)
	{
		$config = $this->getDatabaseConfig();
		$commandMysqlDump = $this->findExecutable('mysqldump');

		$command = '"' . $commandMysqlDump .
			'" -h "' . $config['host'] .
			'" -u "' . $config['username'] . '" ' .
			($config['password'] ? '-p"' . $config['password'] . '"' : '') .
			' "' . $config['database'] . '"' .
			(!$withCreateStatements ? ' --no-create-info '  : '') .
			' --skip-triggers --complete-insert > "' . $file . '"';

		$export = new Process($command);

		if ($export->run() !== 0) {
			throw new RuntimeException(
				'Could not create a backup of the database content.' . PHP_EOL .
				$export->getErrorOutput()
			);
		}
	}

	public function import(string $file)
	{
		$config = $this->getDatabaseConfig();
		$commandMysql = $this->findExecutable('mysql');

		$command = 'cat "' . $file . '" | "' .$commandMysql .
			'" -h "' . $config['host'] .
			'" -u "' . $config['username'] . '" ' .
			($config['password'] ? '-p"' . $config['password'] . '"' : '') .
			' "' . $config['database'] . '"';

		$export = new Process($command);

		if ($export->run() !== 0) {
			throw new RuntimeException('Could not import the current content.' . PHP_EOL . $export->getErrorOutput());
		}
	}

	/**
	 * @return array
	 */
	private function getDatabaseConfig()
	{
		return config('database.connections.mysql');
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function findExecutable($name)
	{
		$executable = $this->executableFinder->find($name);

		if (!$executable) {
			throw new RuntimeException('Could not find executable ' . $name . ' on your system.');
		}

		return $executable;
	}
}
