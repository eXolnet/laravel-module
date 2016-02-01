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

	public function testFacebookInsights()
	{
		// <meta property="fb:admins" content="1234" />
		// <meta property="fb:app_id" content="your_app_id" />
		$this->markTestIncomplete();
	}

	public function testGoogleWebmasterTools()
	{
		// <meta name="google-site-verification" content="TBD">
		$this->markTestIncomplete();
	}

	public function testHealthPageIsAccessible()
	{
		$this->visit('/health')->see('OK');
	}
}
