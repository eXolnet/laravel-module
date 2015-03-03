<?php namespace Exolnet\Session;

class SessionManager extends \Illuminate\Session\SessionManager {

	/**
	 * Get the session options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			'cookie_domain'  => $config['domain'], 'cookie_lifetime' => $config['lifetime'] * 60,
			'cookie_path'    => $config['path'], 'cookie_httponly' => '1', 'name' => $config['cookie'],
			'gc_divisor'     => $config['lottery'][1], 'gc_probability' => $config['lottery'][0],
			'gc_maxlifetime' => $config['files_lifetime'] * 60,
		);
	}

}
