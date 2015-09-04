<?php namespace Exolnet\Test;

use BadMethodCallException;

class TestCaseFunctional extends \TestCase {
	public function __call($method, $args)
	{
		// Setup AJAX query
		if (ends_with($method, 'Ajax')) {
			$this->client->setServerParameter('HTTP_X-Requested-With', 'XMLHttpRequest');
			$this->client->setServerParameter('HTTP_CONTENT_TYPE', 'application/json');
			$this->client->setServerParameter('HTTP_ACCEPT', 'application/json');
			$method = substr($method, 0, -strlen('Ajax'));
		}

		if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
			array_unshift($args, $method);
			return call_user_func_array([$this, 'call'], $args);
		}

		throw new BadMethodCallException;
	}

	protected function displayErrors()
	{
		$errors = $this->app['session.store']->get('notice_error');
		if ($errors) {
			$this->assertSame([], $errors, 'There were errors...');
		}
	}

}
