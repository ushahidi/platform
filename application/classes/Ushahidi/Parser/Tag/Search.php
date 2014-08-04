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
use Ushahidi\Entity\TagSearchData;

class Ushahidi_Parser_Tag_Search implements Parser
{
	public function __invoke(Array $data)
	{
		$data = Arr::extract($data, ['q', 'tag', 'type', 'parent', 'role']);
		// remove any input with an empty value
		$data = array_filter($data);

		return new TagSearchData($data);
	}
}

