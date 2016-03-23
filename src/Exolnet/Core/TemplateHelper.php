<?php namespace Exolnet\Core;

use Illuminate\Support\Str;
use Route;

class TemplateHelper
{
	/**
	 * @return string
	 */
	public static function routeToClass()
	{
		$routeArray = Str::parseCallback(Route::currentRouteAction(), null);

		if (last($routeArray) != null) {
			// Remove 'controller' from the controller name.
			$controller = str_replace('Controller', '', class_basename(head($routeArray)));

			// Take out the method from the action.
			$action = str_replace(['get', 'post', 'patch', 'put', 'delete'], '', last($routeArray));

			return Str::slug($controller . '-' . $action);
		}

		return 'default'; // TODO: Find a better default class name
	}
}