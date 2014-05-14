<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Parser
 *
 * Creates a Tag entity from input parameters
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Tag;

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\Parser as ParserException;

class Ushahidi_Parser_Tag implements Parser
{
	public function __invoke(Array $data)
	{
		if (isset($data['parent']))
		{
			$data['parent_id'] = $data['parent'];
		}
		$valid = Validation::factory($data)
			->rules('tag', array(
					array('is_string'),
				))
			->rules('type', array(
					array('in_array', array(':value', array('category', 'status'))),
				))
			->rules('parent_id', array(
					array('digit'),
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse tag", $valid->errors('tag'));
		}

		return new Tag($valid->as_array());
	}
}
