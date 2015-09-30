<?php namespace Exolnet\Controller;

use Controller;
use Response;

class HealthController extends Controller
{
	/**
	 * Generic /health route to check if the application respond.
	 *
	 * @return \Response
	 */
	public function index()
	{
		return Response::make('OK')->header('Content-Type', 'text/plain');
	}
}
