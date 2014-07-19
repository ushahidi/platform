<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Delete Tag Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;
use Ushahidi\Usecase\Tag\DeleteTagData;

class Ushahidi_Parser_Tag_Delete implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('id', array(
					array('not_empty'),
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse tag delete request", $valid->errors('tag'));
		}

		return new DeleteTagData($data);
	}
}

