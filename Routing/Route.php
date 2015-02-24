<?php namespace Exolnet\Routing;

use Illuminate\Routing\Route as LaravelRoute;
use Lang;

class Route extends LaravelRoute {
	/**
	 * @var string
	 */
	protected $locale;

	protected $baseUri;

	protected static $alternateValidators;

	/**
	 * Create a new Route instance.
	 *
	 * @param  array          $methods
	 * @param  string         $uri
	 * @param  \Closure|array $action
	 * @param                 $locale
	 */
	public function __construct($methods, $uri, $action, $locale)
	{
		$this->locale  = $locale;
		$this->baseUri = preg_replace('/\b'. $locale .'\b/', '%LOCALE%', $uri);
		$uri           = static::translateUri($uri, $locale);

		if (array_key_exists('as', $action)) {
			$action['as'] .= '.' . $locale;
		}

		parent::__construct($methods, $uri, $action);
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setLocale($locale)
	{
		$this->locale = $locale;
		return $this;
	}

	public function getBaseUri()
	{
		return $this->baseUri;
	}

	public function setBaseUri($baseUri)
	{
		$this->baseUri = $baseUri;

		return $this;
	}

	public function setParameters(array $parameters)
	{
		foreach ($parameters as $key => $value) {
			$this->setParameter($key, $value);
		}
	}

	protected static function translateUri($uri, $locale)
	{
		$parts = explode('/', $uri);

		foreach ($parts as $index => $part) {
			$localeKey = 'routes.'.$part;

			if (Lang::has($localeKey, $locale)) {
				$parts[$index] = Lang::get($localeKey, [], $locale);
			}
		}

		return implode('/', $parts);
	}

	//==========================================================================

	public function isAlternate(LaravelRoute $route)
	{
		if ( ! $route instanceof Route) {
			return false;
		}

		// Validate methods
		if ($this->methods() != $route->methods()) {
			return false;
		}

		// Validate scheme
		if ($this->httpOnly() !== $route->httpOnly()) {
			return false;
		}

		// Validate base uri
		if ($this->getBaseUri() !== $route->getBaseUri()) {
			return false;
		}

		if ($this->getUri() === $route->getUri()) {
			return false;
		}

		return true;
	}
}
