<?php

namespace Exolnet\Support;

class Lock
{
	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @var resource
	 */
	private $handle;

	/**
	 * @var bool
	 */
	private $isLocked = false;

	/**
	 * @param $filename
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * Unlocks on destroy.
	 */
	public function __destruct()
	{
		$this->unlock();
	}

	/**
	 * @param bool $shouldBlock
	 * @return bool
	 */
	public function lock($shouldBlock = true)
	{
		if ($this->isLocked) {
			return false;
		}

		$operation = $shouldBlock ? LOCK_EX : LOCK_EX | LOCK_NB;

		$this->handle = fopen($this->filename, 'w+');
		$this->isLocked = flock($this->handle, $operation);

		return $this->isLocked;
	}

	/**
	 * @return bool
	 */
	public function unlock()
	{
		if ( ! $this->isLocked) {
			return true;
		}

		$this->isLocked = ! flock($this->handle, LOCK_UN);
		fclose($this->handle);

		return ! $this->isLocked;
	}

	/**
	 * @param callable $callback
	 * @return mixed
	 */
	public function block(callable $callback)
	{
		$this->lock();
		$value = $callback();
		$this->unlock();

		return $value;
	}
}
