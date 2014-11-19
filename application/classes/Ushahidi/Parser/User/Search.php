<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Search Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Usecase\User\SearchUserData;
use Ushahidi\Core\Traits\Parser\SortingParser;

class Ushahidi_Parser_User_Search implements Parser
{
	use SortingParser;

	// SortingParser
	private function getDefaultOrderby()
	{
		return 'created';
	}

	// SortingParser
	private function getAllowedOrderby()
	{
		// All of these fields have some sort of index in db
		return ['id', 'created', 'updated', 'email', 'username'];
	}

	// SortingParser
	private function getDefaultOrder()
	{
		return 'desc';
	}

	public function __invoke(Array $data)
	{
		$input = Arr::extract($data, ['q', 'role', 'email', 'realname', 'username']);

		// remove any input with an empty value
		$input = array_filter($input);

		// append sorting data
		$input += $this->getSorting($data);

		return new SearchUserData($input);
	}
}

