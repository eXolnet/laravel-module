<?php namespace Exolnet\Test\TestCase\Integration;

use Exolnet\Test\TestCaseFunctional;

class SeoTest extends TestCaseFunctional
{
	/**
	 * Google typically displays the first 50-60 characters of a title tag, or as many characters as
	 * will fit into a 512-pixel display. If you keep your titles under 55 characters, you can
	 * expect at least 95% of your titles to display properly.
	 */
	public function testTitle()
	{
		$this->visit('/')->seeInElement('title', '^\s*$', true);

		$title  = trim($this->crawler->filter('title')->text());
		$actualLength  = strlen($title);
		$maximumLength = 55;

		$this->assertTrue($actualLength <= $maximumLength, 'Page title should be lower than '. $maximumLength .' characters');
	}

	/**
	 * Meta descriptions can be any length, but search engines generally truncate snippets longer
	 * than 160 characters. It is best to keep meta descriptions between 150 and 160 characters.
	 */
	public function testMetaDescription()
	{
		$this->assertMeta('description');

		$description   = $this->crawler->filter('meta[name="description"]')->attr('value');
		$actualLength  = strlen($description);
		$maximumLength = 160;

		$this->assertTrue($actualLength <= $maximumLength, 'Meta description should be lower than '. $maximumLength .' characters');
	}

	/**
	 * Comma separated list of additional keywords for the website.
	 */
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

	public function testRobotsOnOtherEnvironmentsThanProduction()
	{
		$this->markTestIncomplete();
	}

	public function testXmlSiteMap()
	{
		$this->markTestIncomplete();

		// $this->visit('sitemap.xml')->seeStatusCode(200);
	}

	/**
	 * When someone shares content from your site to Facebook, our crawler will scrape the HTML of
	 * the URL that is shared. On a regular HTML page this content is basic and may be incorrect,
	 * because the scraper has to guess which content is important, and which is not.
	 *
	 * Take control of what the Facebook crawler picks up from each page by using Open Graph meta
	 * tags. These tags provide structured info about the page such as the title, description,
	 * preview image, and more.
	 *
	 * @see https://developers.facebook.com/docs/sharing/webmasters#markup
	 */
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

	/**
	 * With Twitter Cards, you can attach rich photos, videos and media experience to Tweets that
	 * drive traffic to your website. Simply add a few lines of HTML to your webpage, and users who
	 * Tweet links to your content will have a “Card” added to the Tweet that’s visible to all of
	 * their followers.
	 *
	 * @see https://dev.twitter.com/cards/overview
	 */
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

	/**
	 * In the world of content management and online shopping systems, it's common for the same
	 * content to be accessed through multiple URLs. With content syndication, it's also easy for
	 * content to be distributed to different URLs and domains entirely. For that purpose we use
	 * the canonical meta to indicate which URL is preferred.
	 *
	 * For example, if a website can be accessed with those addresses:
	 *     - http://example.com/green-dresses
	 *     - http://www.example.com/green-dresses
	 *     - https://example.com/green-dresses
	 *     - https://www.example.com/green-dresses
	 *
	 * And we want search engines to use the https://www. version, we can add the following link:
	 *     <link rel="canonical" href="https://www.example.com/green-dresses">
	 *
	 * @see https://support.google.com/webmasters/answer/139066?hl=en
	 */
	public function testCanonical()
	{
		$this->assertLink('canonical');
	}

	/**
	 * A web application is designed to look and behave in a way similar to a native application—for
	 * example, it is scaled to fit the entire screen on iOS. You can tailor your web application
	 * for Safari on iOS even further, by making it appear like a native application when the user
	 * adds it to the Home screen. You do this by using settings for iOS that are ignored by other
	 * platforms.
	 *
	 * 1. To specify an icon for the entire website (every page on the website), place an icon file in
	 *    PNG format in the root document folder called apple-touch-icon.png
	 *
	 * 2. To specify an icon for a single webpage or replace the website icon with a
	 *    webpage-specific icon, add a link element to the webpage, as in:
	 *
	 *    <link rel="apple-touch-icon" href="/custom_icon.png">
	 *
	 * 3. To specify multiple icons for different device resolutions—for example, support both
	 *    iPhone and iPad devices—add a sizes attribute to each link element as follows:
	 *
	 *    <link rel="apple-touch-icon" href="touch-icon-iphone.png">
	 *    <link rel="apple-touch-icon" sizes="76x76" href="touch-icon-ipad.png">
	 *    <link rel="apple-touch-icon" sizes="120x120" href="touch-icon-iphone-retina.png">
	 *    <link rel="apple-touch-icon" sizes="152x152" href="touch-icon-ipad-retina.png">
	 *
	 * @see https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html
	 */
	public function testAppleTouchIcon()
	{
		$this->assertLink('apple-touch-icon');
	}

	/**
	 * On iOS, similar to native applications, you can specify a startup image that is displayed
	 * while your web application launches. This is especially useful when your web application is
	 * offline. By default, a screenshot of the web application the last time it was launched is
	 * used. To set another startup image, add a link element to the webpage, as in:
	 *
	 *     <link rel="apple-touch-startup-image" href="/startup.png">
	 *
	 * On iPhone and iPod touch, the image must be 320 x 480 pixels and in portrait orientation.
	 *
	 * @see https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html
	 */
	public function testAppleTouchStartupImage()
	{
		$this->assertLink('apple-touch-startup-image');
	}

	/**
	 * On Safari, pinned Sites allow your users to keep their favorite websites open, running, and
	 * easily accessible. You can set the icon that the user sees when they pin your site by
	 * providing a vector image.
	 *
	 * Example: <link rel="mask-icon" href="website_icon.svg" color="red">
	 */
	public function testPinnedTabIcon()
	{
		$this->assertLink('mask-icon');
	}

	/**
	 * The following meta elements control how the Pinned site shortcut is created on the Start
	 * menu and on the Windows 7 taskbar. All these elements are optional, but highly recommended
	 * for more control over where the page starts, its name, better accessibility or recognition,
	 * and customized size at startup. Example:
	 *
	 *    <meta name="msapplication-TileImage" content="/windows-tile.png">
	 *    <meta name="msapplication-TileColor" content="#ffffff">
	 */
	public function testMSApplication()
	{
		$this->assertMeta('msapplication-TileImage');
		$this->assertMeta('msapplication-TileColor');
	}

	/**
	 * It's an initiative for knowing the people behind a website. It's a TXT file that contains
	 * information about the different people who have contributed to building the website.
	 *
	 * @see http://humanstxt.org/
	 */
	public function testHumanTxt()
	{
		$this->assertLink('author', asset('humans.txt'));
	}

	public function testLinkAlternate()
	{
		// Links to an alternate version of the document
		// <link rel="alternate" hreflang="en" href="https://www.exolnet.com/en/services">
		$this->markTestIncomplete();
	}

	public function testLinkHelp()
	{
		// Links to a help document
		$this->markTestIncomplete();
	}

	public function testLinkNext()
	{
		// Indicates that the document is a part of a series, and that the next document in the
		// series is the referenced document
		$this->markTestIncomplete();
	}

	public function testLinkFirst()
	{
		// Indicates that the hyperlink leads to the first resource of the sequence the current page is in.
		$this->markTestIncomplete();
	}

	public function testLinkPrevious()
	{
		// Indicates that the document is a part of a series, and that the previous document in
		// the series is the referenced document
		$this->markTestIncomplete();
	}

	public function testLinkLast()
	{
		// Indicates that the hyperlink leads to the last resource of the sequence the current page is in.
		$this->markTestIncomplete();
	}

	public function testLinkSearch()
	{
		// Links to a search tool for the document
		$this->markTestIncomplete();
	}

	public function testPingback()
	{
		// Defines an external resource URI to call if one make a comment or a citation about the
		// webpage. The protocol used to make such a call is defined in the Pingback 1.0 specification.
		$this->markTestIncomplete();
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
