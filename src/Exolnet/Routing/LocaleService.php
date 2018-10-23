<?php namespace Exolnet\Routing;

use Config;
use Illuminate\Http\Request;
use RuntimeException;

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
        $supportedLocales = Config::get('app.supported_locales');

        if ( ! $supportedLocales ) {
            throw new RuntimeException('You need to define some supported locales (configuration variable "app.supported_locales").');
        }

        return $supportedLocales;
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
