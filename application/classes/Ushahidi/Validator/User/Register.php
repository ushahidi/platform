<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Registration Validator
 *
 * Checks the consistency of the User before registration
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Data;
use Ushahidi\Tool\Validator;
use Ushahidi\Usecase\User\RegisterRepository;
use Ushahidi\Exception\ValidatorException;

class Ushahidi_Validator_User_Register implements Validator
{
	private $repo;

	private $errors = array();

	public function __construct(RegisterRepository $repo)
	{
		$this->repo = $repo;
	}

	public function check(Data $input)
	{
		$valid = Validation::factory($input->asArray())
			->rules('email', array(
					array('not_empty'),
					array('email'),
					array(array($this->repo, 'isUniqueEmail'), array(':value')),
				))
			->rules('username', array(
					array('not_empty'),
					array('max_length', array(':value', 255)),
					array('regex', array(':value', '/^[a-z][a-z0-9._-]+[a-z0-9]$/i')),
					array(array($this->repo, 'isUniqueUsername'), array(':value')),
				))
			->rules('password', array(
					array('not_empty'),
					// Password is hashed at this point, there is no reason to validate length
				));

		$okay = $valid->check();

		if (!$okay)
		{
			throw new ValidatorException("Failed to validate user registration", $valid->errors('user'));
		}

		return true;
	}
}
