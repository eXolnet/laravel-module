<?php namespace Exolnet\Test\TestCase\Integration;

use Exolnet\Test\TestCaseIntegration;

class MonitoringTest extends TestCaseIntegration
{
	public function testUptime()
	{
		$this->markTestIncomplete();
	}

	public function testGraylog()
	{
		$this->markTestIncomplete();
	}

	public function testGoogleAnalytics()
	{
		$this->markTestIncomplete();
	}

	public function testHealthPageIsAccessible()
	{
		$this->visit('/health')->see('OK');
	}
}
