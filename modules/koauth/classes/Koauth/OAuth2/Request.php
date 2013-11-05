<?php

/**
 * Wrapper for OAuth2_Request
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Koauth
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Koauth_OAuth2_Request extends OAuth2_Request implements OAuth2_RequestInterface {

		public static function createFromRequest(Request $request)
		{
			$server = $_SERVER;
			
			// If this is an internal request or request method is empty, fake REQUEST_METHOD
			if ( ! $request->is_initial() OR empty($server['REQUEST_METHOD']))
			{
				$server['REQUEST_METHOD'] = $request->method();
			}
			// If request->post() is empty but we have a request body, parse that
			$post = $request->post(); $body = $request->body();
			if (empty($post) AND ! empty($body))
			{
				parse_str($body, $post);
			}

			// Make sure headers are all upper-case
			// headers expected to be upper case, kohana makes them lower case
			$headers = array_change_key_case( (array) $request->headers(), CASE_UPPER);

			$ret = new static($request->query(), $post, array(), $request->cookie(), $_FILES, $server, $request->body(), $headers);

			return $ret;
		}

	}
