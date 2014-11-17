<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Create Set Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\Set\SetData;

class Ushahidi_Parser_Set_Create implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('name', [
					['not_empty'],
				]);

		return new SetData(Arr::extract($data, ['name', 'filter', 'user_id', 'created', 'updated']));
	}
}
