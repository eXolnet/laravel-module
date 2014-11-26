<?php namespace Exolnet\Database\Eloquent;
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
 * @subpackage Database
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use Exolnet\Core\Exceptions\ValidationException;

class ModelValidationException extends ValidationException {
	/**
	 * The messages list
	 *
	 * @var array
	 */
	protected $messages;

	/**
	 * Constructor
	 *
	 * @param array      $errors
	 * @param string     $message
	 * @param integer    $code
	 * @param exception  $previous
	 */
	public function __construct(array $messages, $code = 0, Exception $previous = null)
	{
		$this->messages = $messages;
		$errors = [];

		array_walk_recursive($messages, function($item) use (&$errors) {
			$errors[] = $item;
		});

		parent::__construct($errors, $code, $previous);
	}

	public function getMessages()
	{
		return $this->messages;
	}

	public function toArray()
	{
		return parent::toArray() + [
			'messages' => $this->getMessages(),
		];
	}
}
