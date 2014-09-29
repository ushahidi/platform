<?php defined('SYSPATH') OR die('No direct script access.');

abstract class HTTP_Exception_Expected extends Kohana_HTTP_Exception_Expected {

	/**
	 * Generate a Response for the current Exception
	 *
	 * @uses   Kohana_Exception::response()
	 * @return Response
	 */
	public function get_response()
	{
		$this->check();

		// Use Kohana_Exception to get a response with a response body
		$response = Kohana_Exception::response($this);
		// Copy headers from $this->_response
		$response->headers((array)$this->_response->headers());
		// Add CORS Headers
		$this->add_cors_headers($response);

		return $response;
	}

}
