<?php namespace Exolnet\Validation;

class UniqueLocale {
	/**
	 * Expected parameters: target model, column, locale, ignore
	 *
	 * @param string $attribute
	 * @param string $value
	 * @param array $parameters
	 * @return bool
	 */
	public function isValid($attribute, $value, array $parameters)
	{
		$model = $parameters[0];
		$field = $parameters[1];
		$locale = $parameters[2];
		$id = $parameters[3];
		$withSoftDelete = (bool)isset($parameters[4]) ?: false;

		$query = $model::whereHas('translations', function ($query) use ($locale, $field, $value) {
			$query->where($field, '=', $value)
				->where('locale', '=', $locale);
		});

		if ($id !== null) {
			$query->where('id', '!=', $id);
		}

		if ($withSoftDelete) {
			$query->withTrashed();
		}

		return $query->count() === 0;
	}
}