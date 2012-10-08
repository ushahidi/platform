<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_HTTP_Exception_502 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 502 Bad Gateway
	 */
	protected $_code = 502;

}