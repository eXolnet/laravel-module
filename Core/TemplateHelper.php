<?php namespace Exolnet\Core;

use Route;
use Str;

class TemplateHelper
{
	public static function routeToClass()
	{
		$routeArray = Str::parseCallback(Route::currentRouteAction(), null);

		if (last($routeArray) != null) {
			// Remove 'controller' from the controller name.
			$controller = str_replace('Controller', '', class_basename(head($routeArray)));

			// Take out the method from the action.
			$action = str_replace(array('get', 'post', 'patch', 'put', 'delete'), '', last($routeArray));

			return Str::slug($controller . '-' . $action);
		}

		return 'default'; // TODO: Find a better default class name
	}
}