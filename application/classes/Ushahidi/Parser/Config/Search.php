<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Config Search Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Usecase\Config\SearchConfigData;

class Ushahidi_Parser_Config_Search implements Parser
{
	public function __invoke(Array $data)
	{
		$input = Arr::extract($data, ['groups']);
		return new SearchConfigData($input);
	}
}
