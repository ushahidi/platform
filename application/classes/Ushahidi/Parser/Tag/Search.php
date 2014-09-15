<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Search Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Usecase\Tag\SearchTagData;
use Ushahidi\Traits\Parser\SortingParser;

class Ushahidi_Parser_Tag_Search implements Parser
{
	use SortingParser;

	// SortingParser
	private function getDefaultOrderby()
	{
		return 'priority';
	}

	// SortingParser
	private function getAllowedOrderby()
	{
		return ['id', 'created', 'tag', 'slug', 'priority'];
	}

	// SortingParser
	private function getDefaultOrder()
	{
		return 'asc';
	}

	public function __invoke(Array $data)
	{
		$input = Arr::extract($data, ['q', 'tag', 'type', 'parent', 'role']);

		// remove any input with an empty value
		$input = array_filter($input);

		// append sorting data
		$input += $this->getSorting($data);

		return new SearchTagData($input);
	}
}

