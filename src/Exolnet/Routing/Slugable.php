<?php namespace Exolnet\Routing;


interface Slugable
{
	/**
	 * Get the value of the model's route slug.
	 *
	 * @return string
	 */
	public function getSlug();
}
