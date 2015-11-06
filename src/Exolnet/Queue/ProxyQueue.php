<?php

namespace Exolnet\Queue;

use Exolnet\Queue\Jobs\ProxyJob;
use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueInterface;

class ProxyQueue extends Queue implements QueueInterface {
	/**
	 * @var \Illuminate\Queue\QueueInterface
	 */
	protected $queue;

	/**
	 * ProxyQueue constructor.
	 *
	 * @param \Illuminate\Queue\QueueInterface $queue
	 */
	public function __construct(QueueInterface $queue)
	{
		$this->queue = $queue;
	}

	/**
	 * Push a new job onto the queue.
	 *
	 * @param  string $job
	 * @param  mixed  $data
	 * @param  string $queue
	 * @return mixed
	 */
	public function push($job, $data = '', $queue = null)
	{
		return $this->queue->push($job, $data, $queue);
	}

	/**
	 * Push a raw payload onto the queue.
	 *
	 * @param  string $payload
	 * @param  string $queue
	 * @param  array  $options
	 * @return mixed
	 */
	public function pushRaw($payload, $queue = null, array $options = [])
	{
		return $this->queue->pushRaw($payload, $queue, $options);
	}

	/**
	 * Push a new job onto the queue after a delay.
	 *
	 * @param  \DateTime|int $delay
	 * @param  string        $job
	 * @param  mixed         $data
	 * @param  string        $queue
	 * @return mixed
	 */
	public function later($delay, $job, $data = '', $queue = null)
	{
		return $this->queue->later($delay, $job, $data, $queue);
	}

	/**
	 * Pop the next job off of the queue.
	 *
	 * @param  string $queue
	 * @return \Illuminate\Queue\Jobs\Job|null
	 */
	public function pop($queue = null)
	{
		$job = $this->queue->pop($queue);
		if ( ! $job) {
			return;
		}
		return new ProxyJob($this->container, $job);
	}
}