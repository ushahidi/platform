<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Create User Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\User\UserData;

class Ushahidi_Parser_User_Create implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('username', array(
					['not_empty'],
				))
			->rules('email', array(
					['not_empty'],
				))
			->rules('password', array(
					['not_empty'],
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse user create request", $valid->errors('user'));
		}

		// Ensure that all properties of a User entity are defined by using Arr::extract
		return new UserData(Arr::extract($data, ['username', 'password', 'realname', 'email', 'role']));
	}
	
}