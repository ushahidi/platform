<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_HTTP_Exception_401 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 401 Unauthorized
	 */
	protected $_code = 401;

}