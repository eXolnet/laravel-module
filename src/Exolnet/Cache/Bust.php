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

		$full_path = public_path() . '/' . $path;

		if ( ! $this->filesystem->exists($full_path)) {
			return asset($path, $secure);
		}

		$time = $this->filesystem->lastModified($full_path);

		if ( ! $time) {
			return asset($path, $secure);
		}

		$basename = pathinfo($path, PATHINFO_BASENAME);
		$pos = strrpos($basename, '.');

		if ($pos === false) {
			return asset($path, $secure);
		}

		$basename_bust = substr($basename, 0, $pos) . '.' . $time . substr($basename, $pos);
		$uri = substr($path, 0, -strlen($basename)) . $basename_bust;

		return asset($uri, $secure);
	}
}
