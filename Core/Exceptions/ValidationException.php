<?php namespace Exolnet\Core\Exceptions;
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
 * @subpackage Core
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use Exception;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;
use JsonSerializable;

class ValidationException extends Exception
	implements ArrayableInterface, JsonableInterface, JsonSerializable
{
	/**
	 * The errors list
	 *
	 * @var array
	 */
	protected $errors;

	/**
	 * Constructor
	 *
	 * @param array      $errors
	 * @param string     $message
	 * @param integer    $code
	 * @param exception  $previous
	 */
	public function __construct($message = null, $code = 0, Exception $previous = null)
	{
		if (is_array($message)) {
			$this->errors = $message;
			$message      = implode(PHP_EOL, $message);
		} else {
			$this->errors = [$message];
		}

		// Construct an exception
		parent::__construct($message, $code, $previous);
	}

	/**
	 * Get the errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Convert the model instance to JSON.
	 *
	 * @param  int  $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'message' => $this->getMessage(),
			'code'    => $this->getCode(),
			'errors'  => $this->getErrors(),
		];
	}
}
