<?php namespace Exolnet\Validation;
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
 * @subpackage Validation
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class Phone
{
	/**
	 * @param $attribute
	 * @param $value
	 * @param $parameters
	 * @return bool
	 */
	public static function isValid($attribute, $value, $parameters)
	{
		try {
			$default_region = 'CA';
			$region = count($parameters) > 0 ? $parameters[0] : $default_region;
			$phoneUtil = self::instance();
			$numberPrototype = $phoneUtil->parse($value, $region);
			return $phoneUtil->isValidNumber($numberPrototype);
		} catch (NumberParseException $e) {
			return false;
		}
	}

	private static function instance()
	{
		return PhoneNumberUtil::getInstance();
	}
}