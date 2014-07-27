<?php namespace Exolnet\Formatter;

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