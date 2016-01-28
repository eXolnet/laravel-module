<?php namespace Exolnet\Test\TestCase\Integration;

use Exolnet\Test\TestCaseIntegration;
use Illuminate\Contracts\Validation\UnauthorizedException;

class QualityTest extends TestCaseIntegration
{
	/**
	 * @var bool
	 */
	protected $hasFavicon;

	public function testCharset()
	{
		$this->visit('/')->seeSelector('meta[charset]');
	}

	public function testXUACompatible()
	{
		$this->visit('/')->seeSelector('meta[http-equiv="X-UA-Compatible"]');
	}

	public function testViewportIsDefined()
	{
		$this->visit('/')->seeSelector('meta[name="viewport"]');
	}

	public function testError404()
	{
		$this->visit('404')->dontSee('Sorry, the page you are looking for could not be found.');
	}

	public function testError403()
	{
		\Route::get('403', function() {
			throw new UnauthorizedException;
		});

		$this->visit('403')->dontSee('UnauthorizedException');
	}

	public function testError500()
	{
		\Route::get('500', function() {
			throw new \RuntimeException;
		});

		$this->visit('500')->dontSee('RuntimeException');
	}

	public function testFavicon()
	{
		if ($this->hasFavicon === null) {
			$this->markTestIncomplete();
			return;
		}

		$this->visit('/');
		$hasMetaIcon = count($this->crawler->filter('meta[name="icon"]')) > 0;

		$hasFaviconIco = $this->visit('favicon.ico')->getStatus() === 200;

		if ($this->hasFavicon) {
			$this->assertTrue($hasMetaIcon || $hasFaviconIco);
		} else {
			$this->assertFalse($hasMetaIcon || $hasFaviconIco);
		}
	}

	public function testAsciiArt()
	{
		$this->visit('/');

		$html = $this->response->getContent();
		$hasAsciiArt = preg_match('/<html(.*?)>\s*<!--/', $html) > 0 || preg_match('/-->\s*<html/', $html) > 0;

		$this->assertTrue($hasAsciiArt);
	}
}
