<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Search Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Usecase\Form\SearchFormData;
use Ushahidi\Core\Traits\Parser\SortingParser;

class Ushahidi_Parser_Form_Search implements Parser
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
		return ['id', 'created', 'updated'];
	}

	public function __invoke(Array $data)
	{
		$input = Arr::extract($data, ['q', 'parent']);

		// remove any input with an empty value
		$input = array_filter($input);

		// append sorting data
		$input += $this->getSorting($data);

		return new SearchFormData($input);
	}
}
