<?php namespace Exolnet\Routing;

use App;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Routing\Router as LaravelRouter;
use Mockery\Exception\RuntimeException;
use Redirect;

class Router extends LaravelRouter
{
	/**
	 * @var array
	 */
	protected $localeStack = [];

	/**
	 * @var \Exolnet\Routing\LocaleService
	 */
	protected $localeService;

	/**
	 * Create a new Router instance.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @param  \Illuminate\Container\Container  $container
	 */
	public function __construct(Dispatcher $events, Container $container = null)
	{
		parent::__construct($events, $container);

		$this->localeService = $this->container->make(LocaleService::class);
	}

	/**
	 * @return string|null
	 */
	protected function getLastLocale()
	{
		if (count($this->localeStack) === 0) {
			return null;
		}

		return end($this->localeStack);
	}

	/**
	 * @return array
	 */
	public function getLocales()
	{
		return $this->localeService->getSupportedLocales();
	}

	/**
	 * @return string
	 */
	public function getBaseLocale()
	{
		return $this->localeService->getBaseLocale();
	}

	/**
	 * @return array
	 */
	public function getAlternateLocales()
	{
		return array_filter($this->getLocales(), function($locale) {
			return App::getLocale() !== $locale;
		});
	}

	/**
	 * @param \Closure $callback
	 * @param array|null $locales
	 * @param bool $avoidPrefixOnBaseLocale
	 */
	public function groupLocales(Closure $callback, array $locales = null, $avoidPrefixOnBaseLocale = false)
	{
		if ($locales === null) {
			$locales = $this->getLocales();
		}

		foreach ($locales as $locale) {
			array_push($this->localeStack, $locale);

			$shouldPrefixLocale = ! $avoidPrefixOnBaseLocale || $this->getBaseLocale() !== $locale;
			$prefix = $shouldPrefixLocale ? $locale : '';

			$this->group(['prefix' => $prefix], $callback);

			array_pop($this->localeStack);
		}
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

		// Since we use the "prefix", Laravel will automatically append it to auto generated
		// resources names. Thus, we may obtain routes named like this "admin.en.page.en" or
		// "en.page.en" (the local is in double). To avoid this, we replace all locales that are
		// not at the end.
		if (array_key_exists('as', $action)) {
			$action['as'] = preg_replace('/(^|\.)'. $locale .'\./', '\1', $action['as']);
		}

		return (new Route($methods, $uri, $action, $locale))->setContainer($this->container);
	}

	//==========================================================================

	/**
	 * @return array
	 */
	public function currentAlternates()
	{
		$route = $this->current();

		if ( ! $route instanceof Route) {
			return [];
		}

		return $route->alternates();
	}

	//==========================================================================

	/**
	 * @param string $route
	 * @param string|array $aliases
	 * @throws \Mockery\Exception\RuntimeException
	 */
	public function alias($route, $aliases)
	{
		$route = $this->getRoutes()->getByName($route);

		if ($route === null) {
			throw new RuntimeException('No route named "'. $route .'" found for alias.');
		}

		foreach ((array)$aliases as $alias) {
			$this->match($route->methods(), $alias, function() use ($route) {
				return Redirect::route($route->getName());
			});
		}
	}
}
