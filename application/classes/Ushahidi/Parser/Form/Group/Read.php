<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Read Form Group Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\Form\ReadFormGroupData;

class Ushahidi_Parser_Form_Group_Read implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('id', [
				['not_empty'],
			])
			->rules('form_id', [
				['not_empty'],
			]);

		if (!$valid->check())
		{
			throw new ParserException('Failed to parse form group read request', $valid->errors('form'));
		}

		return new ReadFormGroupData($data);
	}
}
