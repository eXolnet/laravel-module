<?php namespace Exolnet\Session;
/**
 * Copyright © 2014 eXolnet Inc. All rights reserved. (http://www.exolnet.com)
 *
 * This file contains copyrighted code that is the sole property of eXolnet Inc.
 * You may not use this file except with a written agreement.
 *
 * This code is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND,
 * EITHER EXPRESS OR IMPLIED, AND EXOLNET INC. HEREBY DISCLAIMS ALL SUCH
 * WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT.
 *
 * @package    Exolnet
 * @subpackage Session
 * @author     eXolnet Inc. <info@exolnet.com>
 */

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
