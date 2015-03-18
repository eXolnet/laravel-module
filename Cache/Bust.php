<?php namespace Exolnet\Cache;

class Bust
{
	/**
	 * @param      $path
	 * @param null $secure
	 * @return string
	 */
	public function asset($path, $secure = null)
	{
		$full_path = public_path() . '/' . $path;

		if ( ! file_exists($full_path)) {
			return asset($path, $secure);
		}

		$time = filemtime($full_path);

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
