<?php namespace Exolnet\Database\Eloquent;

use Illuminate\Support\Str;

trait SetFilterTrait
{
	public function getSetFilters()
	{
		return isset($this->setFilters) ? $this->setFilters : [];
	}

	public function getSetFiltersForAttribute($key)
	{
		return array_get($this->getSetFilters(), $key, []);
	}

	public function setAttribute($key, $value)
	{
		parent::setAttribute($key, $value);

		$filtersForAttribute = $this->getSetFiltersForAttribute($key);

		if (empty($filtersForAttribute)) {
			return;
		}

		$value = $this->attributes[$key];

		foreach ($filtersForAttribute as $filter) {
			if (is_callable($filter)) {
				$value = call_user_func($filter, $value);
			}
		}

		$this->attributes[$key] = $value;
	}
}