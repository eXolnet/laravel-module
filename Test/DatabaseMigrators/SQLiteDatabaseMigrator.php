<?php namespace Exolnet\Test\DatabaseMigrators;

use Artisan;
use Illuminate\Filesystem\Filesystem;

class SQLiteDatabaseMigrator extends DatabaseMigrator
{
	protected $booted = false;
	protected $filesytem;

	protected $file;
	protected $cloneFile;

	public function __construct($file)
	{
		parent::__construct();
		$this->filesystem = new Filesystem;

		$this->file = $file;
		$this->cloneFile = $this->getCloneFilename($this->file);
	}

	public function run()
	{
		if (!$this->booted) {
			$this->initialMigration();
			$this->booted = true;
		} else {
			$this->restore();
		}

		// Enable foreign keys for the current connection/file
		\DB::statement('PRAGMA foreign_keys = ON;');
		// Create sqlite-journal in memory only (instead of creating disk files)
		\DB::statement('PRAGMA journal_mode = MEMORY;');
	}

	protected function initialMigration()
	{
		$this->emptyAndChmod($this->file, '');
		$this->emptyAndChmod($this->cloneFile, '');

		Artisan::call('migrate');
		Artisan::call('db:seed');

		$this->filesystem->copy($this->file, $this->cloneFile);
	}

	protected function restore()
	{
		$this->filesystem->copy($this->cloneFile, $this->file);
	}

	protected function emptyAndChmod($file)
	{
		if ($this->filesystem->put($file, '') !== false) {
			chmod($file, 0777);
		}
	}

	protected function getCloneFilename($file)
	{
		$dirname = pathinfo($file, PATHINFO_DIRNAME);
		$filename = pathinfo($file, PATHINFO_BASENAME);
		return $dirname . '/_' . $filename;
	}
}