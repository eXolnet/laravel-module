<?php namespace Exolnet\Test\TestCase\Integration;

use App;
use Exolnet\Test\TestCaseIntegration;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
		$this->get('404');

		$this->assertResponseStatus(404);
		$this->assertNotContains('exception_title', $this->response->getContent());
	}

	public function testError403()
	{
		\Route::get('403', function() {
			App::abort(403);
		});

		$this->get('403');

		$this->assertResponseStatus(403);
		$this->assertNotContains('exception_title', $this->response->getContent());
	}

	public function testError500()
	{
		\Route::get('500', function() {
			throw new HttpException(500);
		});

		$this->get('500');

		$this->assertResponseStatus(500);
		$this->assertNotContains('exception_title', $this->response->getContent());
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

	/**
	 * Styles on a webpage are missing or look incorrect when the page loads in the IE9 when one
	 * stylesheet has more than 4,095 rules.
	 *
	 * @see https://support.microsoft.com/en-us/kb/262161
	 */
	public function testCssSelectorCount()
	{
		$path  = public_path('**/*.css');
		$files = glob($path);
		$maxSelectorCount = 4095;

		foreach ($files as $file) {
			$content = file_get_contents($file);

			$content = preg_replace('/url\(.+?\)/i', 'url(...)', $content);
			$content = preg_replace('/\/\*.*?\*\//s', '', $content);
			$content = preg_replace('/\/\/.*/', '', $content);
			$content = preg_replace('/@(media|font-face).*?\{/', '', $content);

			preg_match_all('/([^{};]+)\{/s', $content, $matches);

			$selectorCount = array_reduce($matches[1], function($count, $selectors) {
				return $count + substr_count($selectors, ',') + 1;
			}, 0);

			$this->assertTrue($selectorCount <= $maxSelectorCount, 'The CSS file "'. $file .'" has '. $selectorCount .' selectors. Having more than '. $maxSelectorCount .' selectors will causes problem on IE9.');
		}
	}

	public function testOpenSearchDescription()
	{
		// <link rel="search" type="application/opensearchdescription+xml" href="/opensearch.xml" title="Website">
		$this->markTestIncomplete();
	}

	public function testDnsPrefetch()
	{
		// <link rel="dns-prefetch" href="//example.com">
		$this->markTestIncomplete();
	}

	public function testPreconnect()
	{
		// <link rel="preconnect" href="http://css-tricks.com">
		$this->markTestIncomplete();
	}

	public function testPrefetch()
	{
		// <link rel="prefetch" href="image.png">
		$this->markTestIncomplete();
	}

	public function testPrerender()
	{
		// <link rel="prerender" href="http://css-tricks.com">
		$this->markTestIncomplete();
	}

	public function testPreload()
	{
		// <link rel="preload" href="image.png">
		$this->markTestIncomplete();
	}
}
