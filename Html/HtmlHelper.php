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
 * @package    Magasin
 * @subpackage Attribute
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
	public static function obfuscateMailtos($html)
	{
		return preg_replace_callback('#("|\')(mailto:.+?)(\1)#', function($match) {
			//dd($match);
			return $match[1]. \HTML::obfuscate($match[2]).$match[3];
		}, $html);
	}
}
