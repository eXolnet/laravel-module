<?php namespace Exolnet\Translation\Traits;

use App;
use Closure;
use Dimsav\Translatable\Translatable as DimsavTranslatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use stdClass;

trait Translatable {
	use DimsavTranslatable {
		DimsavTranslatable::save as translatableSave;
	}

	/**
	 * @param array         $rules
	 * @param array         $customMessages
	 * @param array         $options
	 * @param \Closure|null $beforeSave
	 * @param \Closure|null $afterSave
	 * @return bool
	 */
	public function save(array $rules = [],
						 array $customMessages = [],
						 array $options = [],
						 Closure $beforeSave = null,
						 Closure $afterSave = null)
	{
		if (count($this->translatedAttributes) > 0 && ! $this->translatableSave($options)) {
			return false;
		}
		return parent::save($rules, $customMessages, $options, $beforeSave, $afterSave);
	}

	/**
	 * @param string  $query
	 * @param  string $key
	 * @param    string     $value
	 * @param string|null    $locale
	 * @param string  $op
	 * @return mixed
	 */
	public function scopeHasTranslation($query, $key, $value, $locale = null, $op = '=')
	{
		$locale = $locale ?: App::getLocale();

		return $query->whereHas('translations', function ($q) use ($key, $value, $locale, $op) {
			$q->where('locale', '=', $locale)
				->where($key, $op, $value);
		});
	}

	/**
	 * @param  string    $query
	 * @param  string    $key
	 * @param  string    $op
	 * @param  string    $value
	 * @param string|null $locale
	 * @return mixed
	 */
	public function scopeWhereTranslation($query, $key, $op, $value, $locale = null)
	{
		return $this->scopeHasTranslation($query, $key, $value, $locale, $op);
	}

	public function scopeJoinTranslation(Builder $query, $locale = null)
	{
		$translationTable = $this->getTranslationsTable();
		$localeKey        = $this->getLocaleKey();

		if ($locale === null) {
			$locale = App::getLocale();
		}

		return $query
			->leftJoin($translationTable, $translationTable.'.'.$this->getRelationKey(), '=', $this->getTable().'.'.$this->getKeyName())
			->where($translationTable.'.'.$localeKey, $locale);
	}

	/**
	 * @param string|null $locale
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function translation($locale = null)
	{
		if ( ! $locale) {
			$locale = App::getLocale();
		}

		return $this->hasOne($this->getTranslationModelName(), $this->getRelationKey())
			->where($this->getLocaleKey(), '=', $locale);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function translations()
	{
		return $this->hasMany($this->getTranslationModelName(), $this->getRelationKey());
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getTranslations()
	{
		return $this->translations;
	}

	/**
	 * @param string $key
	 * @param array $values
	 * @return $this
	 */
	public function setAttributeTranslations($key, array $values)
	{
		foreach ($values as $locale => $value) {
			$this->translateOrNew($locale)->setAttribute($key, $value);
		}

		return $this;
	}

	/**
	 * @param array $attributes
	 * @return $this
	 */
	public function fillWithTranslations(array $attributes)
	{
		foreach ($attributes as $key => $value) {
			if ($this->isTranslationAttribute($key)) {
				$this->setAttributeTranslations($key, $value);
			} else {
				$this->setAttribute($key, $value);
			}
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function translationsToArray()
	{
		$translations = [];

		foreach ($this->translations as $translation) {
			$locale = $translation->locale;

			$translations[$locale] = $translation->toArray();
		}

		return $translations;
	}

	/**
	 * @return \stdClass
	 */
	public function translationsAsObject()
	{
		$translations = new stdClass;

		foreach ($this->translations as $translation) {
			$locale = $translation->locale;

			$translations->$locale = $translation;
		}

		return $translations;
	}

	/**
	 * This function allows to build additional parameters to be used in dynamic URL.
	 *
	 * For example, if your route is "/products/{taxonomyType}", you might use the following
	 * function to obtain the alternate parameters:
	 *
	 * $taxonomy->buildAlternateParameters(function(TaxonomyTranslation $translation) {
	 *		return ['taxonomyType' => $translation->getSlug()];
	 * });
	 *
	 * @param callable $callback
	 * @return array
	 */
	public function buildAlternateParameters(callable $callback)
	{
		$localeKey = $this->getLocaleKey();

		return $this->getTranslations()
			->map(function (Model $translation) use ($callback, $localeKey) {
				/** @var array $parameters */
				$parameters = call_user_func($callback, $translation);

				return $parameters + [
					'locale'  => $translation->getAttribute($localeKey),
				];
			})
			->keyBy('locale')
			->toArray();
	}
}
