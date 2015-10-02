<?php namespace Exolnet\Status;

use Controller;
use Illuminate\Filesystem\Filesystem;
use Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StatusController extends Controller
{
	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @param \Illuminate\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}

	/**
	 * Generic /health route to check if the application respond.
	 *
	 * @return \Response
	 */
	public function health()
	{
		return Response::make('OK')->header('Content-Type', 'text/plain');
	}

	/**
	 * @return \Response
	 * @throws \Illuminate\Filesystem\FileNotFoundException
	 */
	public function sha()
	{
		$file = base_path('REVISION');

		if ( ! $this->filesystem->exists($file)) {
			throw new NotFoundHttpException;
		}

		$revision = $this->filesystem->get($file);

		return Response::make($revision)->header('Content-Type', 'text/plain');
	}
}
