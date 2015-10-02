<?php namespace Exolnet\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Validator;

trait ValidationTrait
{
	/**
	 * The last Validator instance.
	 *
	 * @var \Validator
	 */
	protected $validator;

	/**
	 * Get the validation rules.
	 *
	 * @return array
	 */
	public function getRules($attributes = null)
	{
		$rules = isset($this->rules) ? $this->rules : [];
		$rules = $this->buildUniqueRules($rules);

		if ($attributes !== null) {
			if ( ! is_array($attributes)) {
				$attributes = [$attributes];
			}

			// Limit attributes
			$rules = array_intersect_key($rules, array_flip($attributes));
		}

		return $rules;
	}

	/**
	 * Get the last validator.
	 *
	 * @return \Validator|null
	 */
	public function getValidator()
	{
		return $this->validator;
	}

	/**
	 * Get errors for the last validation performed.
	 *
	 * @return array|null
	 */
	public function getValidationErrors()
	{
		if ($this->validator === null) {
			return null;
		}

		return $this->validator->messages()->all();
	}

	/**
	 * Get messages for the last validation performed.
	 *
	 * @return array|null
	 */
	public function getValidatorMessages()
	{
		if ($this->validator === null) {
			return null;
		}

		return $this->validator->messages()->getMessages();
	}

	/**
	 * Validate the object with the rules.
	 *
	 * @return boolean
	 */
	public function validate($attributes = null)
	{
		$this->validator = Validator::make(
			$this->getAttributes(),
			$this->getRules($attributes)
		);

		return $this->validator->passes();
	}

	/**
	 * Validate the model and throw an Exception if it's invalid.
	 *
	 * @throws \Exolnet\Database\Eloquent\ModelValidationException
	 * @return void
	 */
	public function shouldBeValid($attributes = null)
	{
		if ( ! $this->validate($attributes)) {
			$message = $this->getValidatorMessages();

			throw (new ModelValidationException($message))->setValidator($this->validator);
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function performUpdate(Builder $query, array $options = [])
	{
		$this->shouldBeValid();

		return parent::performUpdate($query, $options);
	}

	/**
	 * @inheritDoc
	 */
	protected function performInsert(Builder $query, array $options = [])
	{
		$this->shouldBeValid();

		return parent::performInsert($query, $options);
	}

	/**
	 * Note: Code taken from Ardent::buildUniqueExclusionRules.
	 *
	 * @param array $rules
	 * @return array
	 */
	protected function buildUniqueRules(array $rules = [])
	{
		if ( ! $this->exists) {
			return $rules;
		}

		foreach ($rules as $field => &$ruleset) {
			// If $ruleset is a pipe-separated string, switch it to array
			$ruleset = (is_string($ruleset)) ? explode('|', $ruleset) : $ruleset;

			foreach ($ruleset as &$rule) {
				if (strpos($rule, 'unique') === 0) {
					$params = explode(',', $rule);

					$uniqueRules = [];

					// Append table name if needed
					$table = explode(':', $params[0]);
					if (count($table) === 1) {
						$uniqueRules[1] = $this->table;
					} else {
						$uniqueRules[1] = $table[1];
					}

					// Append field name if needed
					if (count($params) === 1) {
						$uniqueRules[2] = $field;
					} else {
						$uniqueRules[2] = $params[1];
					}

					if (isset($this->primaryKey)) {
						$uniqueRules[3] = $this->{$this->primaryKey};
						$uniqueRules[4] = $this->primaryKey;
					} else {
						$uniqueRules[3] = $this->id;
					}

					$rule = 'unique:' . implode(',', $uniqueRules);
				} // end if strpos unique

			} // end foreach ruleset
		}

		return $rules;
	}
}
