<?php namespace Exolnet\Log;

use Config;
use Exolnet\Log\Processor\LaravelProcessor;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Log as LaravelLog;
use Monolog\Handler\GelfHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\WebProcessor;

class Log {
	public static function prepareForError()
	{
		$monolog = LaravelLog::getMonolog();
		$monolog->pushHandler(new GelfHandler(new Publisher(new UdpTransport(Config::get('log.host', 'localhost'), Config::get('log.port', 12201)))));
		$monolog->pushProcessor(new IntrospectionProcessor());
		$monolog->pushProcessor(new WebProcessor());
		$monolog->pushProcessor(new MemoryUsageProcessor());
		$monolog->pushProcessor(new MemoryPeakUsageProcessor());
		$monolog->pushProcessor(new LaravelProcessor());
	}
}