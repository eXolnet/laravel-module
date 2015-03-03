<?php namespace Exolnet\Database\Eloquent;

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
