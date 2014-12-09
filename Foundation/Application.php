<?php namespace Exolnet\Foundation;
/**
 * Copyright © 2014 eXolnet Inc. All rights reserved. (http://www.exolnet.com)
 *
 * This file contains copyrighted code that is the sole property of eXolnet Inc.
 * You may not use this file except with a written agreement.
 *
 * This code is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND,
 * EITHER EXPRESS OR IMPLIED, AND EXOLNET INC. HEREBY DISCLAIMS ALL SUCH
 * WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT.
 *
 * @package    Exolnet
 * @subpackage Foundation
 * @author     eXolnet Inc. <info@exolnet.com>
 */

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
