<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Password Authenticator
 *
 * Implemented using A1
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\PasswordAuthenticator;
use Ushahidi\Exception\AuthenticatorException;

class Ushahidi_Authenticator_Password implements PasswordAuthenticator
{
	private $a1;

	public function __construct()
	{
		$this->a1 = A1::instance();
	}

	public function checkPassword($plaintext, $hash)
	{
		if (!$this->a1->check($plaintext, $hash)) {
			throw new AuthenticatorException("Password does not match this account");
		}
		return true;
	}
}
