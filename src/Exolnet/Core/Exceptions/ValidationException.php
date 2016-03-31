<?php namespace Exolnet\Core\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Validation\ValidationException as LaravelValidationException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use JsonSerializable;

class ValidationException extends LaravelValidationException implements Arrayable, Jsonable, JsonSerializable
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
	public function __construct($message = null, $code = 0)
	{
		if (is_array($message)) {
			$this->errors = $message;
		} else {
			$this->errors = [$message];
		}

		$messages = new MessageBag($this->errors);

		// Construct an exception
		parent::__construct($messages);

		$this->code = $code;
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
	 * @param array $errors
	 * @return $this
	 */
	public function setErrors(array $errors)
	{
		$this->errors = $errors;

		return $this;
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
	 * @param \Illuminate\Http\Request $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function toResponse(Request $request)
	{
		$errors = $this->getMessageProvider()->getMessageBag()->toArray();

		if ($request->ajax() || $request->wantsJson()) {
			return response()->json($errors, 422);
		}

		return redirect()->back()->withInput()->withErrors($errors);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @return \HttpResponseException
	 */
	public function toHttpResponseException(Request $request)
	{
		return new HttpResponseException($this->toResponse($request));
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
