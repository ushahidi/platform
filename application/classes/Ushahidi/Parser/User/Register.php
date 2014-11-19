<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Registration Parser
 *
 * Creates a User entity from registration details, hashes the password
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Tool\Hasher;
use Ushahidi\Core\Exception\ParserException;

use Ushahidi\Core\Usecase\User\RegisterData;

class Ushahidi_Parser_User_Register implements Parser
{
	private $hasher;

	public function __construct(Hasher $hasher)
	{
		$this->hasher = $hasher;
	}

	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('csrf', [
					['not_empty'],
					['Security::check']
				])
			->rules('email', [
					['not_empty'],
					['email']
				])
			->rules('verify_email', [
					['matches', [':validation', ':field', 'email']],
				])
			->rules('username', [
					['not_empty'),
				])
			->rules('password', [
					['not_empty'],
					// NOTE: Password should allow ANY character at all. Do not limit to alpha numeric or alpha dash.
					['min_length', [':value', 7]],
					['max_length', [':value', 72]], // Bcrypt max length is 72
					// todo: The statement that bcrypt has a max length is wrong. It will
					// *truncate* passwords longer than 72 characters with *some* methods.
					// But when it does truncate, the hash should still pass...
				]);

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse user registration", $valid->errors('user'));
		}

		$data['password'] = $this->hasher->hash($data['password']);

		return new RegisterData($data);
	}
}
