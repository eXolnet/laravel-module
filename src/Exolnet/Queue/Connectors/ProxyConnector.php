<?php

namespace Exolnet\Queue\Connectors;

use Exolnet\Queue\ProxyQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Queue\QueueManager;

class ProxyConnector implements ConnectorInterface {
	/**
	 * @var \Illuminate\Queue\QueueManager
	 */
	protected $manager;

	/**
	 * @param \Illuminate\Queue\QueueManager $manager
	 */
	public function __construct(QueueManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * @param  array $config
	 * @return \Illuminate\Queue\QueueInterface
	 */
	public function connect(array $config)
	{
		return new ProxyQueue($this->manager->connection($config['connection']));
	}
}