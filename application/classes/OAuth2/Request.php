<?php defined('SYSPATH') or die('No direct script access');

/**
 * OAuth2 Kohana Request Proxy
 *
 * License is MIT, to be more compatible with PHP League.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\OAuth2
 * @copyright  2014 Ushahidi
 * @license    http://mit-license.org/
 * @link       http://github.com/php-loep/oauth2-server
 */

use League\OAuth2\Server\Util\RequestInterface;

class OAuth2_Request implements RequestInterface {

	public function __construct()
	{
		$this->request = Request::current();
	}

	public function get($index = null)
	{
		return $this->request->query($index);
	}

	public function post($index = null)
	{
		return $this->request->post($index);
	}

	public function cookie($index = null)
	{
		return Arr::get($_COOKIE, $index);
	}

	public function file($index = null)
	{
		return Arr::get($_FILE, $index);
	}

	public function server($index = null)
	{
		return Arr::get($_SERVER, $index);
	}

	public function header($index = null)
	{
		return $this->request->headers($index);
	}
}

