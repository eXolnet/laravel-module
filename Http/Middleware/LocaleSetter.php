<?php

namespace Exolnet\Http\Middleware;

use Closure;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleSetter {
	/**
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$locale = $this->extractLocale($request);

		App::setLocale($locale);
		// TODO-TR: Support something else than _CA.utf8 <trochette@exolnet.com>
		setlocale(LC_COLLATE, $locale . '_CA.utf8');
		setlocale(LC_CTYPE, $locale . '_CA.utf8');
		setlocale(LC_TIME, $locale . '_CA.utf8');

		return $next($request);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @return string
	 */
	protected function extractLocale(Request $request)
	{
		// 1. Try to extract the locale by with the first URI segment
		$locale = $request->segment(1);

		if ($this->isSupportedLocale($locale)) {
			return $locale;
		}

		// Default locale
		return $this->getBaseLocale();
	}

	/**
	 * @return array
	 */
	protected function getSupportedLocales()
	{
		return Config::get('app.supported_locales', []);
	}

	/**
	 * @param string $locale
	 * @return bool
	 */
	protected function isSupportedLocale($locale)
	{
		return in_array($locale, $this->getSupportedLocales());
	}

	/**
	 * @return string
	 */
	protected function getBaseLocale()
	{
		return Config::get('locale', reset($this->getSupportedLocales()));
	}
}