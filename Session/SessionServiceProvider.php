<?php namespace Exolnet\Session;

class SessionServiceProvider extends \Illuminate\Session\SessionServiceProvider {
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->setupDefaultDriver();

		$this->registerSessionManager();

		$this->registerSessionDriver();
	}

	/**
	 * Register the session manager instance.
	 *
	 * @return void
	 */
	protected function registerSessionManager()
	{
		$this->app['session.manager'] = $this->app->share(function($app)
		{
			return new SessionManager($app);
		});
	}
}
