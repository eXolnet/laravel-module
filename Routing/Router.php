<?php namespace Exolnet\Routing;

use App;
use Closure;
use Config;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Routing\Router as LaravelRouter;

class Router extends LaravelRouter
{
	/**
	 * @var array
	 */
	protected $localeStack = [];

	/**
	 * @var array
	 */
	protected $supportedLocales = [];

	//==========================================================================

	public function groupLocales(Closure $callback, array $locales = null)
	{
		if ($locales === null) {
			$locales = $this->getSupportedLocales();
		}

		foreach ($locales as $locale) {
			array_push($this->localeStack, $locale);

			$this->group(['prefix' => $locale], $callback);

			array_pop($this->localeStack);
		}
	}

	public function getLastLocale()
	{
		if (count($this->localeStack) === 0) {
			return null;
		}

		return end($this->localeStack);
	}

	/**
	 * @docInherit
	 */
	protected function newRoute($methods, $uri, $action)
	{
		if (count($this->localeStack) === 0) {
			return parent::newRoute($methods, $uri, $action);
		}

		$locale = end($this->localeStack);

		return new Route($methods, $uri, $action, $locale);
	}

	/**
	 * @docInherit
	 */
	protected function getGroupResourceName($prefix, $resource, $method)
	{
		$prefix = parent::getGroupResourceName($prefix, $resource, $method);
		$locale = $this->getLastLocale();

		if ($locale === null) {
			return $prefix;
		}

		return str_replace($locale . '.', '', $prefix);
	}

	//==========================================================================

	/**
	 * @docInherit
	 */
	public function dispatch(Request $request)
	{
		// Set locale
		$initialLocale = App::getLocale();
		$locale = $this->extractLocale($request);

		App::setLocale($locale);
		setlocale(LC_COLLATE, $locale . '_CA.utf8');
		setlocale(LC_CTYPE, $locale . '_CA.utf8');
		setlocale(LC_TIME, $locale . '_CA.utf8');

		// Dispatch request
		$response = parent::dispatch($request);

		// Reset the locale
		App::setLocale($initialLocale);

		return $response;
	}

	//==========================================================================

	public function getSupportedLocales()
	{
		return Config::get('app.supported_locales', []);
	}

	public function isSupportedLocale($locale)
	{
		return in_array($locale, $this->getSupportedLocales());
	}

	public function getBaseLocale()
	{
		return Config::get('app.locale', reset($this->getSupportedLocales()));
	}

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

	//==========================================================================

	public function currentAlternates()
	{
		$route = $this->current();

		if ($route === null) {
			return [];
		}

		return $this->alternates($route);
	}

	public function alternates(LaravelRoute $route)
	{
		if ( ! $route instanceof Route) {
			return [];
		}

		$alternates = [];
		$parameters = $route->parameters();

		foreach ($this->routes as $alternate) {
			if ($route->isAlternate($alternate)) {
				$alternates[] = $alternate;
			}
		}

		return $alternates;
	}

	protected function getResourceAction($resource, $controller, $method, $options)
	{
		$name = $this->getResourceName($resource, $method, $options);
		$options = array_except($options, ['as', 'uses']);

		return ['as' => $name, 'uses' => $controller.'@'.$method] + $options;
	}
}
