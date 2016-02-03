<?php namespace Exolnet\Html;

use Collective\Html\HtmlBuilder as LaravelHtmlBuilder;

class HtmlHelper
{
	/**
	 * Obfuscate all mailto in the HTML source provided.
	 *
	 * @param $value
	 * @return mixed
	 */
	public static function obfuscateEmails($html)
	{
		return preg_replace_callback('#(mailto:)?[a-z0-9_.+-]+@[a-z0-9-]+\.[a-z0-9-.]+#i', function ($match) {
			return \HTML::email($match[0]);
		}, $html);
	}
}
