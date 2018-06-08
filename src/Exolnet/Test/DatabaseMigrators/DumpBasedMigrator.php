<?php

namespace Exolnet\Test\DatabaseMigrators;

abstract class DumpBasedMigrator extends DatabaseMigrator
{
	/**
	 * @var string
	 */
	protected $identifier;
	/**
	 * @var string Filename of the backup file that will be cloned in future tests
	 */
	protected $cloneFile;

	public function __construct(string $identifier)
	{
		parent::__construct();
		$this->identifier = $identifier;
		$this->cloneFile = $this->getCloneFilename($this->identifier);
	}

	protected function initialMigration(): void
	{
		$signature = $this->calculateFilesSignature();
		if ($this->canReuseClone($signature)) {
			$this->restore();
			return;
		}

		$this->initializeFiles();

		parent::initialMigration();

		$this->save();

		$this->generateBOM($signature);
	}

	protected function initializeFiles(): void
	{
		$this->createDirectory();
		$this->emptyAndChmod($this->cloneFile);
	}

	protected function createDirectory()
	{
		$directory = base_path('database/states');
		if (!$this->filesystem->exists($directory)) {
			$this->filesystem->makeDirectory($directory);
		}
	}

	protected function emptyAndChmod(string $file): void
	{
		if ($this->filesystem->put($file, '') !== false) {
			$this->filesystem->chmod($file, 0777);
		}
	}

	protected function canReuseClone($signature): bool
	{
		return $this->bomFileExists() && $this->cloneSignatureMatches() && $this->filesSignatureMatches($signature);
	}

	protected function bomFileExists(): bool
	{
		$bomFilename = $this->getBOMFilename($this->identifier);
		return $this->filesystem->exists($bomFilename);
	}

	protected function filesSignatureMatches($signature): bool
	{
		$data = $this->getBOMData();

		return $signature === $data->files;
	}

	protected function cloneSignatureMatches(): bool
	{
		if ( ! $this->filesystem->exists($this->cloneFile)) {
			return false;
		}

		$cloneFileHash = sha1($this->filesystem->get($this->cloneFile));

		$data = $this->getBOMData();

		return $cloneFileHash === $data->clone;
	}

	protected function getBOMData(): object
	{
		$bomFilename = $this->getBOMFilename($this->identifier);
		return json_decode($this->filesystem->get($bomFilename));
	}

	protected function getCloneFilename(string $identifier): string
	{
		return base_path('database/states/_' . $identifier);
	}

	protected function getBOMFilename(string $identifier): string
	{
		return base_path('database/states/' . $identifier . '.json');
	}

	protected function generateBOM(string $signature): void
	{
		$data = [
			'files'  => $signature,
			'clone' => sha1($this->filesystem->get($this->cloneFile)),
		];
		$this->filesystem->put($this->getBOMFilename($this->identifier), json_encode($data));
	}
}
