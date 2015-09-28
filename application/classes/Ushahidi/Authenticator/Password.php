<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Password Authenticator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\PasswordAuthenticator;
use Ushahidi\Core\Exception\AuthenticatorException;

class Ushahidi_Authenticator_Password implements PasswordAuthenticator
{
	public function checkPassword($plaintext, $hash)
	{
		if (!password_verify($plaintext, $hash)) {
			throw new AuthenticatorException("Password does not match this account");
		}
		return true;
	}
}
