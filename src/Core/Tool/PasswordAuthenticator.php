<?php

/**
 * Ushahidi Platform Password Authenticator Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface PasswordAuthenticator
{
	/**
	 * @param  string password in plain text
	 * @param  string stored password hash
	 * @return boolean
	 * @throws Ushahidi\Core\Exception\AuthenticatorException
	 */
	public function checkPassword($plaintext, $hash);
}
