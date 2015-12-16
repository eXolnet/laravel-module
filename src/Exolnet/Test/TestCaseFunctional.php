<?php namespace Exolnet\Test;

use BadMethodCallException;
use Illuminate\View\View;
use PHPUnit_Framework_Assert as PHPUnit;

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

	public function assertViewResponse($viewName = null)
	{
		if (! isset($this->response->original) || ! $this->response->original instanceof View) {
			return PHPUnit::assertTrue(false, 'The response was not a view.');
		}

		if ($viewName !== null) {
			PHPUnit::assertEquals($viewName, $this->response->original->name(), 'Failed asserting the view responded.');
		}
	}

	public function assertSessionDoesntHave($key)
	{
		if (is_array($key)) {
			return $this->assertSessionDoesntHaveAll($key);
		}

		PHPUnit::assertFalse($this->app['session.store']->has($key), "Session contains key: $key");

	}

	public function assertSessionDoesntHaveAll($keys)
	{
		foreach($keys as $key){
			if(! $this->assertSessionDoesntHave($key))
				return false;
		}

		return true;
	}

}
