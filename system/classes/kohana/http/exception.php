<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_HTTP_Exception extends Kohana_Exception {

	/**
	 * @var     int      http status code
	 */
	protected $_code = 0;

	/**
	 * Creates a new translated exception.
	 *
	 *     throw new Kohana_Exception('Something went terrible wrong, :user',
	 *         array(':user' => $user));
	 *
	 * @param   string  $message    status message, custom content to display with error
	 * @param   array   $variables  translation variables
	 * @param   integer $code       the http status code
	 * @return  void
	 */
	public function __construct($message = NULL, array $variables = NULL, $code = 0)
	{
		if ($code == 0)
		{
			$code = $this->_code;
		}

		if ( ! isset(Response::$messages[$code]))
			throw new Kohana_Exception('Unrecognized HTTP status code: :code . Only valid HTTP status codes are acceptable, see RFC 2616.', array(':code' => $code));

		parent::__construct($message, $variables, $code);
	}

} // End Kohana_HTTP_Exception