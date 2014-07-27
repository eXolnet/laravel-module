<?php namespace Exolnet\Cache;
/**
 * Copyright © 2014 eXolnet Inc. All rights reserved. (http://www.exolnet.com)
 * This file contains copyrighted code that is the sole property of eXolnet Inc.
 * You may not use this file except with a written agreement.
 *
 * This code is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND,
 * EITHER EXPRESS OR IMPLIED, AND EXOLNET INC. HEREBY DISCLAIMS ALL SUCH
 * WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT.
 *
 * @package      Exolnet
 * @subpackage   Bust
 * @author       eXolnet <info@exolnet.com>
 */

class Bust {
	/**
	 * @param      $path
	 * @param null $secure
	 * @return string
	 */
	public function asset($path, $secure = null)
	{
		$full_path = public_path().'/'.$path;

		if ( ! file_exists($full_path)) {
			return asset($path, $secure);
		}

		$time = filemtime($full_path);

		if ( ! $time) {
			return asset($path, $secure);
		}

		$basename = pathinfo($path, PATHINFO_BASENAME);
		$pos      = strpos($basename, '.');

		if ($pos === false) {
			return asset($path, $secure);
		}

		$basename_bust = substr($basename, 0, $pos) . '.' . $time . substr($basename, $pos);
		$uri           = substr($path, 0, -strlen($basename)) . $basename_bust;

		return asset($uri, $secure);
	}
}
