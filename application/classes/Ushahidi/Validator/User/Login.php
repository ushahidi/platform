<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Login Validator
 *
 * Checks the consistency of the User before login
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity;
use Ushahidi\Tool\Validator;
use Ushahidi\Usecase\User\LoginRepository;
use Ushahidi\Exception\ValidatorException;

class Ushahidi_Validator_User_Login implements Validator
{
	private $errors = array();

	public function check(Entity $user)
	{
		$valid = Validation::factory($user->asArray())
			->rules('username', array(
					array('not_empty'),
					array('max_length', array(':value', 255)),
					array('regex', array(':value', '/^[a-z][a-z0-9._-]+[a-z0-9]$/i')),
				))
			->rules('password', array(
					array('not_empty'),
					// No reason to validate length here, even though the password
					// is plaintext, because we always want to run the hash check.
				));

		$okay = $valid->check();

		if (!$okay)
		{
			throw new ValidatorException("Failed to validate user registration", $valid->errors('user'));
		}

		return true;
	}
}

