<?php

Route::get('health', [
	'as' => 'health',
	'uses' => '\Exolnet\Controller\StatusController@health',
]);

Route::get('sha', [
	'as' => 'sha',
	'uses' => '\Exolnet\Controller\StatusController@sha',
]);
