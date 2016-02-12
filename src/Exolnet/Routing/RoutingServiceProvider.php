<?php namespace Exolnet\Routing;

use Illuminate\Routing\RoutingServiceProvider as ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
	/**
	 * Register the router instance.
	 *
	 * @return void
	 */
	protected function registerRouter()
	{
		$this->app['router'] = $this->app->share(function ($app) {
			return new Router($app['events'], $app);
		});
	}

	/**
	 * Register the URL generator service.
	 *
	 * @return void
	 */
	protected function registerUrlGenerator()
	{
		$this->app['url'] = $this->app->share(function ($app) {
			// The URL generator needs the route collection that exists on the router.
			// Keep in mind this is an object, so we're passing by references here
			// and all the registered routes will be available to the generator.
			$routes = $app['router']->getRoutes();

			return new UrlGenerator($routes, $app->rebinding('request', function ($app, $request) {
				$app['url']->setRequest($request);
			}));
		});
	}
}
