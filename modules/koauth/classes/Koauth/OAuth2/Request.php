<?php

/**
 * Wrapper for OAuth2_Request
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Koauth
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Koauth_OAuth2_Request extends OAuth2_Request implements OAuth2_RequestInterface {

		public static function createFromRequest(Request $request)
		{
			return new static($request->query(), $request->post(), array(), $request->cookie(), $_FILES, $_SERVER, $request->headers());
		}

	}
