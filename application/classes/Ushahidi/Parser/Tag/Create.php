<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Create Tag Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;
use Ushahidi\Usecase\Tag\TagData;

class Ushahidi_Parser_Tag_Create implements Parser
{
	public function __invoke(Array $data)
	{
		if (empty($data['slug']) AND !empty($data['tag']))
		{
			// todo: this won't work well for non-English tag titles.
			//       a quick survery (of one: Al Jezeera) shows that it might be
			//       better to use UUID for everything. need to address this for
			//       Arabic translations.
			$data['slug'] = URL::title($data['tag']);
		}

		$valid = Validation::factory($data)
			->rules('tag', array(
					array('not_empty'),
				))
			->rules('slug', array(
					array('not_empty'),
				))
			->rules('type', array(
					array('not_empty'),
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse tag create request", $valid->errors('tag'));
		}

		// Ensure that all properties of a Tag entity are defined by using Arr::extract
		return new TagData(Arr::extract($data, ['tag', 'slug', 'description', 'type', 'color', 'icon', 'priority', 'role']));
	}
}

