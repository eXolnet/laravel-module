<?php namespace Exolnet\Formatter;
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
 * @subpackage Formatter
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Phone
{
	/**
	 * @param        $value
	 * @param string $region
	 * @return string
	 */
	public static function format($value, $region = 'CA')
	{
		try {
			$value = trim($value);

			$phoneUtil = self::instance();
			$numberPrototype = $phoneUtil->parse($value, $region);

			$format = $numberPrototype->getCountryCode() === 1 && substr($value, 0, 1) !== '+'
				? PhoneNumberFormat::NATIONAL
				: PhoneNumberFormat::E164;

			$value = $phoneUtil->format($numberPrototype, $format);
		} catch (NumberParseException $e) {
			// Do nothing
		}
		return $value;
	}

	private static function instance()
	{
		return PhoneNumberUtil::getInstance();
	}
}