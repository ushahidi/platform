<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * OAuth2 Exception class to proxy error responses from OAuth2_Response library
 */

abstract class Koauth_OAuth2_Exception extends HTTP_Exception {

	/**
	 * @var  Response   Response Object
	 */
	protected $_oauth2_response;

	/**
	 * @var  string  error view content type
	 */
	public static $error_view_content_type = 'application/json';
	
	/**
	 * Creates a new oauth exception.
	 *
	 * @param   OAuth2_Response  $oauth2_response    OAuth2 Response object
	 * @return  void
	 */
	public function __construct(OAuth2_Response $oauth2_response)
	{
		$this->_oauth2_response = $oauth2_response;
		
		$message = "error : error_description";
		$variables = $this->_oauth2_response->getParameters();
		// Set status code
		$this->_code = $this->_oauth2_response->getStatusCode();
		
		parent::__construct($message, $variables);
	}

	/**
	 * Generate a Response for the current Exception
	 * 
	 * @return Response
	 */
	public function get_response()
	{
		// Prepare the response object.
		$response = Response::factory()
			->status($this->code)
			->headers('Content-Type', self::$error_view_content_type.'; charset='.Kohana::$charset)
			->headers($this->_oauth2_response->getHttpHeaders())
			->body(json_encode($this->_oauth2_response->getParameters()));
		
		return $response;
	}
	
}
