<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Update User Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\User\UpdateUserData;

class Ushahidi_Parser_User_Update implements Parser
{

	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('id', array(
					['not_empty'],
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse user update request", $valid->errors('user'));
		}

		$data['id'] = (int)$data['id'];

		return new UpdateUserData(Arr::extract($data, ['id', 'username', 'password', 'realname', 'email', 'role']));
	}
}

