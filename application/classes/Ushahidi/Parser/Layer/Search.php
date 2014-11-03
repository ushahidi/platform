<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Layer Search Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Traits\Parser\SortingParser;
use Ushahidi\Core\Usecase\Layer\SearchLayerData;

class Ushahidi_Parser_Layer_Search implements Parser
{
	use SortingParser;

	// SortingParser
	private function getDefaultOrderby()
	{
		return 'id';
	}

	// SortingParser
	private function getAllowedOrderby()
	{
		return ['id', 'created', 'active', 'type'];
	}

	// SortingParser
	private function getDefaultOrder()
	{
		return 'asc';
	}

	public function __invoke(Array $data)
	{
		$data = Arr::extract($data, ['active', 'type']);

		// remove any input with an empty value
		if ($data['type'] === NULL)
		{
			unset($data['type']);
		}

		if ($data['active'] === NULL)
		{
			unset($data['active']);
		}

		// append sorting data
		$data += $this->getSorting($data);

		return new SearchLayerData($data);
	}
}

