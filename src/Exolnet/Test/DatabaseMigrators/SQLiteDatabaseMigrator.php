<?php namespace Exolnet\Test\DatabaseMigrators;

use Artisan;

class SQLiteDatabaseMigrator extends DumpBasedMigrator
{
	/**
	 * @var string Filename of the file expected by Laravel as SQLite database
	 */
	protected $file;

	/**
	 * @param string $file
	 */
	public function __construct(string $file)
	{
		$identifier = pathinfo($file, PATHINFO_BASENAME);
		parent::__construct($identifier);

		$this->file = $file;
	}

	protected function initializeFiles(): void
	{
		parent::initializeFiles();
		$this->emptyAndChmod($this->file);
	}

	protected function configurePragma(): void
	{
		// Enable foreign keys for the current connection/file
		\DB::statement('PRAGMA foreign_keys = ON;');
		// Create sqlite-journal in memory only (instead of creating disk files)
		\DB::statement('PRAGMA journal_mode = MEMORY;');
		// Do not wait for OS after sending write commands
		\DB::statement('PRAGMA synchronous = OFF;');
	}

	protected function migrate(): void
	{
		$this->configurePragma();
		parent::migrate();
	}

	protected function save(): void
	{
		$this->filesystem->copy($this->file, $this->cloneFile);
	}

	protected function restore(): void
	{
		$this->filesystem->copy($this->cloneFile, $this->file);

		$this->configurePragma();
	}
}
