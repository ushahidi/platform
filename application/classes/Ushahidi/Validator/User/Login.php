<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Login Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\User\LoginRepository;

class Ushahidi_Validator_User_Login extends Validator
{
	protected $default_error_source = 'user';

	protected function getRules()
	{
		return [
			'email' => [
				['not_empty'],
			],
			'password' => [
				['not_empty'],
				// No reason to validate length here, even though the password
				// is plaintext, because we always want to run the hash check.
			],
		];
	}
}
