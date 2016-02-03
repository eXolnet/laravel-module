<?php namespace Exolnet\Core\Exceptions;

use Exception;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Validation\Validator;
use JsonSerializable;

class ValidationException extends Exception implements Arrayable, Jsonable, JsonSerializable
{
	/**
	 * The errors list
	 *
	 * @var array
	 */
	protected $errors;
	/**
	 * @var \Illuminate\Validation\Validator
	 */
	protected $validator;

	/**
	 * Constructor
	 *
	 * @param array|string $message
	 * @param integer      $code
	 * @param exception    $previous
	 */
	public function __construct($message = null, $code = 0, Exception $previous = null)
	{
		if (is_array($message)) {
			$this->errors = $message;
			$message = implode(PHP_EOL, $message);
		} else {
			$this->errors = [$message];
		}

		// Construct an exception
		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return \Illuminate\Validation\Validator
	 */
	public function getValidator()
	{
		return $this->validator;
	}

	/**
	 * @param \Illuminate\Validation\Validator $validator
	 * @return $this
	 */
	public function setValidator(Validator $validator)
	{
		$this->validator = $validator;

		return $this;
	}

	/**
	 * @param \Illuminate\Validation\Validator $validator
	 * @param string|null                      $message
	 * @param int                              $code
	 * @param \Exception                       $previous
	 * @return static
	 */
	public static function fromValidator(Validator $validator, $message = null, $code = 0, Exception $previous = null)
	{
		return (new static($message ?: $validator->errors()->all(), $code, $previous))->setValidator($validator);
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
	 * @param  int $options
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
