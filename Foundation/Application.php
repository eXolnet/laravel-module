<?php namespace Exolnet\Foundation;

use \Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication {
	/**
	 * Remove all data contained within the application.
	 */
	public function reset()
	{
		// Container reset
		$this->resolved = [];
		$this->bindings = [];
		$this->instances = [];
		$this->aliases = [];
		$this->reboundCallbacks = [];
		$this->resolvingCallbacks = [];
		$this->globalResolvingCallbacks = [];

		// Application reset
		$this->bootingCallbacks = [];
		$this->bootedCallbacks = [];
		$this->finishCallbacks = [];
		$this->shutdownCallbacks = [];
		$this->middlewares = [];
		$this->serviceProviders = [];
		$this->loadedProviders = [];
		$this->deferredServices = [];
	}

	/**
	 * Set the current application locale.
	 *
	 * @param  string  $locale
	 * @return void
	 */
	public function setLocale($locale)
	{
		parent::setLocale($locale);

		$localeMapping = $this['config']->get('locale-mapping');

		$mappedValue = array_get($localeMapping, 'locales.'.$locale);
		if ($mappedValue) {
			$mappedCategories = array_get($localeMapping, 'categories');
			if (in_array(LC_ALL, $mappedCategories)) {
				setlocale(LC_ALL, $mappedValue);
			} else {
				foreach ($mappedCategories as $mappedCategory) {
					setlocale($mappedCategory, $mappedValue);
				}
			}
		}
	}
}
