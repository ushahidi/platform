<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_422 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 400 Bad Request
	 */
	protected $_code = 422;

	protected $errors;

	/**
	 * Creates a new translated exception.
	 *
	 *     throw new Kohana_Exception('Something went terrible wrong, :user',
	 *         array(':user' => $user));
	 *
	 * @param   string  $message    status message, custom content to display with error
	 * @param   array   $variables  translation variables
	 * @return  void
	 */
	public function __construct($message = NULL, array $variables = NULL, Exception $previous = NULL, Array $errors = NULL)
	{
		if ($errors) {
			$this->setErrors($errors);
		}

		if (method_exists($previous, 'getErrors')) {
			$this->setErrors($previous->getErrors());
		}

		parent::__construct($message, $variables, $previous);
	}

	public function setErrors(Array $errors)
	{
		$this->errors = $errors;
	}

	public function getErrors()
	{
		return $this->errors ?: array();
	}

}
