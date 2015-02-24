<?php namespace Exolnet\Routing;

use App;
use Config;
use Illuminate\Routing\UrlGenerator as LaravelUrlGenerator;

class UrlGenerator extends LaravelUrlGenerator {
	public function route($name, $parameters = array(), $absolute = true, $route = null, $locale = null)
	{
		$name = $this->getBestRouteName($name, $locale);

		return parent::route($name, $parameters, $absolute, $route);
	}

	// Is it possible to do something for this kind of method?
	// public function to($path, $extra = array(), $secure = null)
	// {
	//
	// }

	// Is it possible to do something for this kind of method?
	// public function action($action, $parameters = array(), $absolute = true)
	// {
	//
	// }

	protected function getBestRouteName($name, $locale = null)
	{
		if ($this->routes->getByName($name) !== null) {
			return $name;
		}

		// Check for a route with the current locale
		if ($locale === null) {
			$locale = App::getLocale();
		}

		if ($locale === null) {
			return $name;
		}

		$localeName = $name.'.'.$locale;

		if ($this->routes->getByName($localeName) !== null) {
			return $localeName;
		}

		return $name;
	}

	public function cdn($path, $secure = null)
	{
		$root = $this->getCdnUrl($secure);

		return $this->removeIndex($root).'/'.trim($path, '/');
	}

	public function getCdnUrl($secure = null)
	{
		$cdn_url = Config::get('app.cdn_url');

		return $this->getRootUrl($this->getScheme($secure), $cdn_url);
	}
}
