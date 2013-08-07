<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_401 extends Kohana_HTTP_Exception_401 {
	
	

	/**
	 * Generate a Response for the current Exception
	 * 
	 * @uses   Kohana_Exception::response()
	 * @return Response
	 */
	public function get_response()
	{
		$this->check();

		return Kohana_Exception::response($this);
	}
	
}