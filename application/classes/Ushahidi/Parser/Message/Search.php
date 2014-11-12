<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Message Search Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Usecase\Message\SearchMessageData;
use Ushahidi\Core\Traits\Parser\SortingParser;

class Ushahidi_Parser_Message_Search implements Parser
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
		return ['id', 'created'];
	}

	// SortingParser
	private function getDefaultOrder()
	{
		return 'desc';
	}

	public function __invoke(Array $data)
	{
		$input = Arr::extract($data, ['q', 'type', 'parent', 'contact', 'data_provider', 'post', 'box', 'status']);

		// remove any input with an empty value
		$input = array_filter($input);

		// append sorting data
		$input += $this->getSorting($data);

		return new SearchMessageData($input);
	}
}

