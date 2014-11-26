<?php namespace Exolnet\Html;
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
 * @subpackage Html
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use \Illuminate\Html\HtmlBuilder as LaravelHtmlBuilder;

class HtmlHelper {
	/**
	 * Obfuscate all mailto in the HTML source provided.
	 *
	 * @param $value
	 * @return mixed
	 */
	public static function obfuscateEmails($html)
	{
		return preg_replace_callback('#(mailto:)?[a-z0-9_.+-]+@[a-z0-9-]+\.[a-z0-9-.]+#i', function($match) {
			return \HTML::email($match[0]);
		}, $html);
	}
}
