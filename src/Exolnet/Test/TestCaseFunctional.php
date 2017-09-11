<?php namespace Exolnet\Test;

use BadMethodCallException;
use Illuminate\View\View;
use PHPUnit_Framework_Assert as PHPUnit;

/**
 * @method \Illuminate\Http\Response get($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response getAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response postAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response postAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response putAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response putAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response patchAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response patchAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response deleteAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 * @method \Illuminate\Http\Response deleteAjax($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
 */
abstract class TestCaseFunctional extends \Tests\TestCase {
	public function __call($method, $args)
	{
		// Setup AJAX query
		$isAjaxQuery = false;
		if (ends_with($method, 'Ajax')) {
			$method = substr($method, 0, -strlen('Ajax'));
			$isAjaxQuery = true;
		}

		if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
			array_unshift($args, $method);
			if ($isAjaxQuery) {
				call_user_func_array([$this, 'json'], $args);
			} else {
				call_user_func_array([$this, 'call'], $args);
			}
			return $this->response;
		}

		throw new BadMethodCallException;
	}

	protected function displayErrors()
	{
		$errors = $this->app['session.store']->get('notice_error');
		$errors = $errors ?: $this->app['session.store']->get('errors');
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
		return ! $this->assertSessionHasAll($keys);
	}

	public function setPreviousUrl($url)
	{
		$this->app['session.store']->setPreviousUrl($url);

		return $this;
	}
}
