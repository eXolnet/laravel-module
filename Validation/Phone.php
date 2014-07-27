<?php namespace Exolnet\Validation;

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