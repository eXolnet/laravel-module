<?php namespace Exolnet\Test\Traits;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use View;

trait AssertionsTrait {
	public function assertViewExists($view_name, $message = 'The view %s was not found.')
	{
		try {
			View::make($view_name);
			$this->assertTrue(true);
		} catch (InvalidArgumentException $e) {
			$this->fail(sprintf($message, $view_name));
		}
	}

	public function assertHttpException($expectedStatusCode, Closure $testCase)
	{
		try {
			$testCase($this);

			$this->assertFalse(true, "An HttpException should have been thrown by the provided Closure.");
		} catch (HttpException $e) {
			// assertResponseStatus() won't work because the response object is null
			$this->assertEquals(
				$expectedStatusCode,
				$e->getStatusCode(),
				sprintf("Expected an HTTP status of %d but got %d.", $expectedStatusCode, $e->getStatusCode())
			);
		}
	}

	public function expectResponseAccessDenied(Closure $testCase)
	{
		$this->assertHttpException(403, $testCase);
	}

	public function expectResponseMissing()
	{
		$this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
	}

	public function assertResponseContentType($expected)
	{
		$response = $this->client->getResponse();

		$actual = $response->headers->get('Content-type');

		return $this->assertEquals($expected, $actual, 'Expected response '.$expected.', got '.$actual.'.');
	}

	public function assertResponseJson()
	{
		return $this->assertResponseContentType('application/json');
	}

	public function assertRouteExists($method, $uri, $message = null)
	{
		$message = $message ?: sprintf('The route %s %s was not found.', strtoupper($method), $uri);

		// Create a corresponding request
		$request = Request::create($uri, $method);

		// Match the request to a route
		$route = $this->app['router']->getRoutes()->match($request);
		$this->assertNotNull($route, $message);
	}

	public function assertRouteMatchesAction($method, $uri, $action, $message = null)
	{
		$message = $message ?: sprintf('The route %s %s does not match action %s.', strtoupper($method), $uri, $action);

		// Create a corresponding request
		$request = Request::create($uri, $method);

		// Match the request to a route
		$route = $this->app['router']->getRoutes()->match($request);
		$condition = $route === null || $route->getAction()['controller'] !== $action;
		$this->assertFalse($condition, $message);
	}

	public function assertIsViewResponse($response)
	{
		$this->assertInstanceOf('Illuminate\View\View', $response);
	}

	public function assertIsRedirectResponse($response)
	{
		$this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
	}

	public function assertResponseRedirectedTo($response, $uri, $with = array())
	{
		$this->assertIsRedirectResponse($response);

		$this->assertEquals($this->app['url']->to($uri), $response->headers->get('Location'));

		$this->assertSessionHasAll($with);
	}

	public function assertResponseRedirectedToRoute($response, $name, $parameters = array(), $with = array())
	{
		$this->assertResponseRedirectedTo($response, $this->app['url']->route($name, $parameters), $with);
	}

	public function assertResponseRedirectedToAction($response, $name, $parameters = array(), $with = array())
	{
		$this->assertResponseRedirectedTo($response, $this->app['url']->action($name, $parameters), $with);
	}

	public function assertIsJsonResponse($response)
	{
		$this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
	}

	public function assertIsStreamResponse($response)
	{
		$this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
	}

	public function assertNotice($type)
	{
		$this->assertSessionHas('notice_'.$type);
	}

	/**
	 * Asserts if two arrays have similar values, sorting them before the fact
	 * in order to "ignore" ordering.
	 *
	 * @param array $actual
	 * @param array $expected
	 * @param string $message
	 * @param float $delta
	 * @param int $depth
	 */
	protected function assertArrayValuesEquals(array $expected, array $actual, $message = '', $delta = 0.0, $depth = 10)
	{
		$this->assertEquals($expected, $actual, $message, $delta, $depth, true);
	}
}