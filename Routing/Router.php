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

	/**
	 * @param \Closure   $callback
	 * @param array|null $locales
	 */
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

	/**
	 * @return string|null
	 */
	public function getLastLocale()
	{
		if (count($this->localeStack) === 0) {
			return null;
		}

		return end($this->localeStack);
	}

	/**
	 * @param array|string $methods
	 * @param string       $uri
	 * @param string       $action
	 * @return \Exolnet\Routing\Route|\Illuminate\Routing\Route
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
	 * @param string $prefix
	 * @param string $resource
	 * @param string $method
	 * @return string
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

	/**
	 * @return array
	 */
	protected function getSupportedLocales()
	{
		return Config::get('app.supported_locales', []);
	}

	//==========================================================================

	/**
	 * @return array
	 */
	public function currentAlternates()
	{
		$route = $this->current();

		if ($route === null) {
			return [];
		}

		return $this->alternates($route);
	}

	/**
	 * @param \Illuminate\Routing\Route $route
	 * @return array
	 */
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

	/**
	 * @param string $resource
	 * @param string $controller
	 * @param string $method
	 * @param string $options
	 * @return array
	 */
	protected function getResourceAction($resource, $controller, $method, $options)
	{
		$name = $this->getResourceName($resource, $method, $options);
		$options = array_except($options, ['as', 'uses']);

		return ['as' => $name, 'uses' => $controller.'@'.$method] + $options;
	}
}
