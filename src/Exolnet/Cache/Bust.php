<?php namespace Exolnet\Cache;

use Illuminate\Filesystem\Filesystem;

class Bust
{
	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var bool
	 */
	protected $enabled = true;

	/**
	 * Bust constructor.
	 *
	 * @param \Illuminate\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem)
	{

		$this->filesystem = $filesystem;
	}

	/**
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * @param bool $enabled
	 * @return $this
	 */
	public function setEnabled($enabled)
	{
		$this->enabled = (bool)$enabled;

		return $this;
	}

	/**
	 * Generate an asset path for the application with the file's last modification timestamp
	 * to avoid cashing.
	 *
	 * @param  string    $path
	 * @param  bool|null $secure
	 * @return string
	 */
	public function asset($path, $secure = null)
	{
		if ( ! $this->isEnabled()) {
			return asset($path, $secure);
		}

		$uri = $this->path($path);

		return asset($uri, $secure);
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function path($path)
	{
		if ( ! $this->isEnabled()) {
			return $path;
		}

		$fullPath = public_path() . '/' . $path;

		if ( ! $this->filesystem->exists($fullPath)) {
			return $path;
		}

		$time = $this->filesystem->lastModified($fullPath);

		if ( ! $time) {
			return $path;
		}

		$basename = pathinfo($path, PATHINFO_BASENAME);
		$pos = strrpos($basename, '.');

		if ($pos === false) {
			return $path;
		}

		$basename_bust = substr($basename, 0, $pos) . '.' . $time . substr($basename, $pos);
		return substr($path, 0, -strlen($basename)) . $basename_bust;
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function filename($path)
	{
		return basename($this->path($path));
	}
}
