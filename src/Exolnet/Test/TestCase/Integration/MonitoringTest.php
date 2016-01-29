<?php namespace Exolnet\Test\TestCase\Integration;

use Exolnet\Test\TestCaseIntegration;

class MonitoringTest extends TestCaseIntegration
{
	public function testGraylog()
	{
		$this->markTestIncomplete();
	}

	public function testGoogleAnalytics()
	{
		$this->visit('/')->seeInElement('script', '//www.google-analytics.com/analytics.js');
	}

	public function testHealthPageIsAccessible()
	{
		$this->visit('/health')->see('OK');
	}
}
