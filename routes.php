<?php

Route::get('health', [
	'as' => 'health',
	'uses' => '\Exolnet\Controller\HealthController@index',
]);
