<?php

Route::get('health', [
	'as' => 'health',
	'uses' => '\Exolnet\Status\StatusController@health',
]);

Route::get('sha', [
	'as' => 'sha',
	'uses' => '\Exolnet\Status\StatusController@sha',
]);
