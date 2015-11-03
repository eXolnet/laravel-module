<?php

namespace Exolnet\Queue\Jobs;

use Illuminate\Queue\Jobs\Job;

class ProxyJob {
	/**
	 * @var \Illuminate\Queue\Jobs\Job
	 */
	protected $job;

	/**
	 * TenantJob constructor.
	 *
	 * @param \Illuminate\Queue\Jobs\Job $job
	 */
	public function __construct(Job $job)
	{
		$this->job = $job;
	}

	/**
	 * Fire the job.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->job->fire();
	}

	/**
	 * Release the job back into the queue.
	 *
	 * @param  int $delay
	 * @return void
	 */
	public function release($delay = 0)
	{
		$this->job->release($delay);
	}

	/**
	 * Get the number of times the job has been attempted.
	 *
	 * @return int
	 */
	public function attempts()
	{
		$this->job->attempts();
	}

	/**
	 * Get the raw body string for the job.
	 *
	 * @return string
	 */
	public function getRawBody()
	{
		return $this->job->getRawBody();
	}
}