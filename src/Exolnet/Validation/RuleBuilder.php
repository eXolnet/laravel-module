<?php namespace Exolnet\Validation;

class RuleBuilder
{
	public function forLanguage(array $rules, $languages)
	{
		return $this->mapKeyedLanguage($rules, (array)$languages);
	}

	private function mapKeyedLanguage(array $data, array $languages)
	{
		$result = [];
		foreach ($data as $key => $value) {
			foreach ($languages as $language) {
				$newKey = str_replace('$lang', $language, $key);
				$newValue = str_replace('$lang', $language, $value);
				$result[$newKey] = $newValue;
			}
		}
		return $result;
	}
}
