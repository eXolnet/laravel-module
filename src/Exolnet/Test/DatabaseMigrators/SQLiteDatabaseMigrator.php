<?php namespace Exolnet\Test\DatabaseMigrators;

use Artisan;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

class SQLiteDatabaseMigrator extends DatabaseMigrator
{
	protected $booted = false;
	protected $filesytem;

	protected $file;
	protected $cloneFile;

	/**
	 * @param $file
	 */
	public function __construct($file)
	{
		parent::__construct();
		$this->filesystem = new Filesystem;

		$this->file = $file;
		$this->cloneFile = $this->getCloneFilename($this->file);
	}

	public function run()
	{
		if ( ! $this->booted) {
			$this->initialMigration();
			$this->booted = true;
		} else {
			$this->restore();
		}
	}

	protected function configurePragma()
	{
		// Enable foreign keys for the current connection/file
		DB::statement('PRAGMA foreign_keys = ON;');
		// Create sqlite-journal in memory only (instead of creating disk files)
		DB::statement('PRAGMA journal_mode = MEMORY;');
		// Do not wait for OS after sending write commands
		DB::statement('PRAGMA synchronous = OFF;');
	}

	protected function initialMigration()
	{
		$signature = $this->calculateFilesSignature();
		if ($this->canReuseClone($signature)) {
			$this->restore();
			return;
		}

		$this->emptyAndChmod($this->file, '');
		$this->emptyAndChmod($this->cloneFile, '');

		$this->configurePragma();

		Artisan::call('migrate');
		if (file_exists(base_path('database/seeds/TestSeeder.php'))) {
			Artisan::call('db:seed', ['--class' => 'TestSeeder']);
		}

		$this->filesystem->copy($this->file, $this->cloneFile);

		$this->generateBOM($signature);
	}

	protected function restore()
	{
		$this->filesystem->copy($this->cloneFile, $this->file);

		$this->configurePragma();
	}

	/**
	 * @param $file
	 */
	protected function emptyAndChmod($file)
	{
		if ($this->filesystem->put($file, '') !== false) {
			chmod($file, 0777);
		}
	}

	/**
	 * @param $file
	 * @return string
	 */
	protected function getCloneFilename($file)
	{
		$dirname = pathinfo($file, PATHINFO_DIRNAME);
		$filename = pathinfo($file, PATHINFO_BASENAME);
		return $dirname . '/_' . $filename;
	}

	protected function canReuseClone($signature)
	{
		return $this->bomFileExists() && $this->sqliteSignatureMatches() && $this->signatureMatches($signature);
	}

	protected function bomFileExists()
	{
		$bomFilename = $this->getBOMFilename($this->file);
		return $this->filesystem->exists($bomFilename);
	}

	protected function signatureMatches($signature)
	{
		$data = $this->getBOMData();

		return $signature === $data->files;
	}

	protected function sqliteSignatureMatches()
	{
		if ( ! $this->filesystem->exists($this->cloneFile)) {
			return false;
		}

		$cloneFileHash = sha1($this->filesystem->get($this->cloneFile));

		$data = $this->getBOMData();

		return $cloneFileHash === $data->sqlite;
	}

	protected function getBOMData()
	{
		$bomFilename = $this->getBOMFilename($this->file);
		return json_decode($this->filesystem->get($bomFilename));
	}

	protected function calculateFilesSignature()
	{
		$files = glob(base_path('database/{migrations,seeds}/*.php'), GLOB_BRACE);

		$signature = '';
		foreach ($files as $file) {
			$signature .= sha1($this->filesystem->get($file));
		}
		return sha1($signature);
	}

	protected function getBOMFilename($file)
	{
		$dirname = pathinfo($file, PATHINFO_DIRNAME);
		$filename = pathinfo($file, PATHINFO_BASENAME);
		return $dirname . '/' . $filename . '.json';
	}

	protected function generateBOM($signature)
	{
		$data = [
			'files'  => $signature,
			'sqlite' => sha1($this->filesystem->get($this->cloneFile)),
		];
		$this->filesystem->put($this->getBOMFilename($this->file), json_encode($data));
	}
}
