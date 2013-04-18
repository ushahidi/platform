<?php

/**
 * Wrapper for OAuth2_Request
 */
class Kohana_OAuth2_Request extends OAuth2_Request implements OAuth2_RequestInterface {
		
		/*public function query($name, $default = null)
		{
			return $this->kohana_request->query($name);
		}

		public function request($name, $default = null)
		{
			return $this->kohana_request->post($name);
		}

		public function headers($name, $default = null)
		{
			return $this->kohana_request->headers($name);
		}

		public function getAllQueryParameters()
		{
			return $this->kohana_request->query();
		}*/

		public static function createFromRequest(Request $request)
		{
			return new static($request->query(), $request->post(), array(), $request->cookie(), $_FILES, $_SERVER);
		}

	}
