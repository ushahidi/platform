<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Create Media Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;
use Ushahidi\Usecase\Media\MediaData;

class Ushahidi_Parser_Media_Create implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('file', array(
					array('not_empty'),
					array('is_array'),
				))
			->rules('user_id', array(
					array('digit'),
				))
			->rules('caption', array(
					array('is_string'),
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse media create request", $valid->errors('media'));
		}

		// Ensure that all properties for MediaData are defined by using Arr::extract
		return new MediaData(Arr::extract($data, ['file', 'user_id', 'caption']));
	}
}
