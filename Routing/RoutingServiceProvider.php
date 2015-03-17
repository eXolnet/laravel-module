<?php namespace Exolnet\Routing;

class RoutingServiceProvider extends \Illuminate\Routing\RoutingServiceProvider
{
	protected function registerRouter()
	{
		$this->app['router'] = $this->app->share(function($app)
		{
			$router = new Router($app['events'], $app);

			// If the current application environment is "testing", we will disable the
			// routing filters, since they can be tested independently of the routes
			// and just get in the way of our typical controller testing concerns.
			if ($app['env'] == 'testing') {
				$router->disableFilters();
			}

			return $router;
		});
	}

	protected function registerUrlGenerator()
	{
		$this->app['url'] = $this->app->share(function($app)
		{
			// The URL generator needs the route collection that exists on the router.
			// Keep in mind this is an object, so we're passing by references here
			// and all the registered routes will be available to the generator.
			$routes = $app['router']->getRoutes();

			return new UrlGenerator($routes, $app->rebinding('request', function($app, $request)
			{
				$app['url']->setRequest($request);
			}));
		});
	}
}
