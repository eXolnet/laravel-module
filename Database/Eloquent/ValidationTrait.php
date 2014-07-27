<?php namespace Exolnet\Database\Eloquent;
/**
 * Copyright © 2014 eXolnet Inc. All rights reserved. (http://www.exolnet.com)
 * This file contains copyrighted code that is the sole property of eXolnet Inc.
 * You may not use this file except with a written agreement.
 *
 * This code is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND,
 * EITHER EXPRESS OR IMPLIED, AND EXOLNET INC. HEREBY DISCLAIMS ALL SUCH
 * WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT.
 *
 * @package      Exolnet
 * @subpackage   Database
 * @author       eXolnet <info@exolnet.com>
 */

use Illuminate\Database\Eloquent\Builder;
use Validator;

trait ValidationTrait {
	/**
	 * The last Validator instance.
	 *
	 * @var Validator
	 */
	protected $validator;

	/**
	 * Get the validation rules.
	 *
	 * @return array
	 */
	public function getRules()
	{
		return isset($this->rules) ? $this->rules : [];
	}

	/**
	 * Get the last validator.
	 *
	 * @return Validator|null
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
	public function validate()
	{
		$this->validator = Validator::make(
			$this->getAttributes(),
			$this->getRules()
		);

		return $this->validator->passes();
	}

	/**
	 * Validate the model and throw an Exception if it's invalid.
	 *
	 * @throws ModelValidationException
	 * @return void
	 */
	public function shouldBeValid()
	{
		if ( ! $this->validate()) {
			$message = $this->getValidatorMessages();

			throw new ModelValidationException($message);
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function performUpdate(Builder $query)
	{
		$this->shouldBeValid();

		return parent::performUpdate($query);
	}

	/**
	 * @inheritDoc
	 */
	protected function performInsert(Builder $query)
	{
		$this->shouldBeValid();

		return parent::performInsert($query);
	}
}
