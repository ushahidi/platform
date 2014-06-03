<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Login Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Data;
use Ushahidi\Tool\Validator;
use Ushahidi\Usecase\User\LoginRepository;

class Ushahidi_Validator_User_Login implements Validator
{
	private $valid;

	public function check(Data $input)
	{
		$this->valid = Validation::factory($input->asArray())
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

		return $this->valid->check();
	}

	public function errors($from = 'user')
	{
		return $this->valid->errors($from);
	}
}
