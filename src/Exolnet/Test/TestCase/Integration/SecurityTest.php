<?php namespace Exolnet\Test\TestCase\Integration;

use Exolnet\Test\TestCaseIntegration;

class SecurityTest extends TestCaseIntegration
{
	public function testXFrameOptions()
	{
		$this->assertHeaderInHtaccess('X-Frame-Options');
	}

	public function testXXssProtection()
	{
		$this->assertHeaderInHtaccess('X-XSS-Protection');
	}

	public function testXContentTypeOptions()
	{
		$this->assertHeaderInHtaccess('X-Content-Type-Options');
	}

	public function testStrictTransportSecurity()
	{
		$this->assertHeaderInHtaccess('Strict-Transport-Security');
	}

	public function testPublicKeyPins()
	{
		$this->assertHeaderInHtaccess('Public-Key-Pins');
	}

	public function testContentSecurityPolicy()
	{
		$this->assertHeaderInHtaccess('Content-Security-Policy');
	}

	/**
	 * @param string $name
	 */
	private function assertHeaderInHtaccess($name)
	{
		$htaccessPath = public_path('.htaccess');

		$this->assertTrue(file_exists($htaccessPath));

		$content = file_get_contents($htaccessPath);
		$hasHeader = preg_match('/^[^#]*Header\s+set\s+'. preg_quote($name) .'/mi', $content) > 0;

		$this->assertTrue($hasHeader);
	}
}
