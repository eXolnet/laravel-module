<?php namespace Exolnet\Test\TestCase\Integration;

use Exolnet\Test\TestCaseIntegration;

class SecurityTest extends TestCaseIntegration
{
	public function testXFrameOptions()
	{
		$this->markTestIncomplete();
		//$this->visit('/')->seeHeader('X-Frame-Options');
	}

	public function testXXssProtection()
	{
		$this->markTestIncomplete();
		//$this->visit('/')->seeHeader('X-XSS-Protection');
	}

	public function testXContentTypeOptions()
	{
		$this->markTestIncomplete();
		//$this->visit('/')->seeHeader('X-Content-Type-Options');
	}

	public function testStrictTransportSecurity()
	{
		$this->markTestIncomplete();
		//$this->visit('/')->seeHeader('Strict-Transport-Security');
	}

	public function testPublicKeyPins()
	{
		$this->markTestIncomplete();
		//$this->visit('/')->seeHeader('Public-Key-Pins');
	}

	public function testContentSecurityPolicy()
	{
		$this->markTestIncomplete();
		//$this->visit('/')->seeHeader('Content-Security-Policy');
	}
}
