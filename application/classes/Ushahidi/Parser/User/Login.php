<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Login Parser
 *
 * Creates a User entity from login details
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;

use Ushahidi\Usecase\User\LoginData;

class Ushahidi_Parser_User_Login implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('csrf', array(
					array('not_empty'),
					array('Security::check')
				))
			->rules('username', array(
					array('not_empty'),
				))
			->rules('password', array(
					array('not_empty'),
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse user login", $valid->errors('user'));
		}

		// NOTE: never hash the password in the login parser! We need the plaintext
		// password to pass through to the authenticator. In addtion, every password
		// hash is unique, even for the same password, so we cannot compare two
		// hashes directly.

		return new LoginData($data);
	}
}
