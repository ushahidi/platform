<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_429 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 429 Too Many Requests
	 */
	protected $_code = 429;

}
