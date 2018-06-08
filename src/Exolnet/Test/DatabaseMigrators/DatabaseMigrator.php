<?php namespace Exolnet\Test\DatabaseMigrators;

use Artisan;
use Illuminate\Filesystem\Filesystem;
use Schema;

class DatabaseMigrator
{
	/**
	 * @var bool Is it the first time we run the migrator?
	 */
	protected $booted = false;
	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $filesytem;

	/**
	 * @param string $file
	 */
	public function __construct()
	{
		$this->filesystem = new Filesystem;
	}

	public function run(): void
	{
		if ( ! $this->booted) {
			$this->initialMigration();
			$this->booted = true;
		} else {
			$this->restore();
		}
	}

	protected function initialMigration(): void
	{
		$this->reset();
		$this->migrate();
		$this->seed();
	}

	protected function reset(): void
	{
		if (Schema::hasTable('migrations')) {
			Artisan::call('migrate:reset');
		}
	}

	protected function migrate(): void
	{
		Artisan::call('migrate');
	}

	protected function seed(): void
	{
		if (file_exists(base_path('database/seeds/TestSeeder.php'))) {
			Artisan::call('db:seed', ['--class' => 'TestSeeder']);
		}
	}

	protected function save(): void
	{
		// Do nothing
	}

	protected function restore(): void
	{
		$this->initialMigration();
	}

	protected function calculateFilesSignature(): string
	{
		$files = glob(base_path('database/{migrations,seeds}/*.php'), GLOB_BRACE);

		$signature = '';
		foreach ($files as $file) {
			$signature .= sha1($this->filesystem->get($file));
		}
		return sha1($signature);
	}
}
