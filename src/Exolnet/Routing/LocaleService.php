<?php namespace Exolnet\Routing;

use Config;
use Illuminate\Http\Request;

class LocaleService
{
	/**
	 * @return string
	 */
	public function getBaseLocale()
	{
		return Config::get('app.locale', 'en');
	}

	/**
	 * @return array
	 */
	public function getSupportedLocales()
	{
		return Config::get('app.supported_locales', []);
	}

	/**
	 * @param string $locale
	 * @return bool
	 */
	public function isSupportedLocale($locale)
	{
		return in_array($locale, $this->getSupportedLocales());
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @return string
	 */
	public function extractLocale(Request $request)
	{
		// 1. Try to extract the locale by with the first URI segment
		$locale = $request->segment(1);

		if ($this->isSupportedLocale($locale)) {
			return $locale;
		}

		// Default locale
		return $this->getBaseLocale();
	}
}
