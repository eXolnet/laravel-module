<?php namespace Exolnet\Core\Exceptions;

use \Exception;

class ValidationException extends Exception {
	/**
	 * The errors list
	 * @var array
	 */
	protected $errors;

	/**
	 * Class constructor
	 * @param string     $message
	 * @param array      $errors
	 * @param integer    $code
	 * @param exception  $previous
	 */
	public function __construct($message = null, $errors, $code = 0, Exception $previous = null)
	{
		// Array of errors
		$this->errors = $errors;

		// Construct an exception
		parent::__construct($message, $code, $previous);
	}

	/**
	 * Get the errors
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}