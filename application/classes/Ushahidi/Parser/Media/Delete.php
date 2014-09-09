<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Delete Media Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;
use Ushahidi\Usecase\Media\MediaDeleteData;

class Ushahidi_Parser_Media_Delete implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('id', array(
					array('not_empty'),
					array('digit'),
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse media delete request", $valid->errors('media'));
		}

		return new MediaDeleteData($data);
	}
}

