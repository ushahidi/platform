<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Update Tag Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;
use Ushahidi\Usecase\Tag\TagData;

class Ushahidi_Parser_Tag_Update implements Parser
{
	public function __invoke(Array $data)
	{
		$input = new TagData($data);

		if (!empty($input->color)) {
			$input->color = ltrim($input->color, '#');
		}

		return $input;
	}
}

