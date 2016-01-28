<?php namespace Exolnet\Test\TestCase\Integration;

use Exolnet\Test\TestCaseFunctional;

class SeoTest extends TestCaseFunctional
{
	public function testTitle()
	{
		$this->visit('/')->seeInElement('title', '^\s*$', true);
	}

	public function testMetaDescription()
	{
		$this->assertMeta('description');
	}

	public function testMetaKeywords()
	{
		$this->assertMeta('keywords');
	}

	public function testRobotTxt()
	{
		$this->markTestIncomplete();

		// $this->visit('robot.txt')
		// 	->see('User-agent: *')
		// 	->see('Disallow:');
	}

	public function testXmlSiteMap()
	{
		$this->markTestIncomplete();

		// $this->visit('sitemap.xml')->seeStatusCode(200);
	}

	public function testOpenGraphTags()
	{
		$this->assertMeta('og:locale');
		$this->assertMeta('og:title');
		$this->assertMeta('og:image');
		$this->assertMeta('og:description');
		$this->assertMeta('og:url');
		$this->assertMeta('og:type');
		$this->assertMeta('og:site_name');
	}

	public function testTwitterCard()
	{
		$this->assertMeta('twitter:card');
		$this->assertMeta('twitter:title');
		$this->assertMeta('twitter:description');
		$this->assertMeta('twitter:image');
		$this->assertMeta('twitter:domain');
	}

	public function testGooglePublisher()
	{
		$this->assertLink('Publisher');
	}

	public function testCanonical()
	{
		$this->assertLink('canonical');
	}

	public function testAppleTouchIcon()
	{
		$this->assertLink('apple-touch-icon');
	}

	public function testHumanTxt()
	{
		$this->assertLink('author', asset('humans.txt'));
	}

	//==============================================================================================

	protected function assertMeta($name, $value = null)
	{
		$valueSelector = $value ? '[value="'. $value .'"]' : ':not([value=""])';

		$this->visit('/')->seeSelector('meta[name="'. $name .'"]'. $valueSelector);
	}

	protected function assertLink($rel, $href = null)
	{
		$hrefSelector = $href ? '[href="'. $href .'"]' : ':not([value=""])';

		$this->visit('/')->seeSelector('link[rel="'. $rel .'"]'. $hrefSelector);
	}
}
