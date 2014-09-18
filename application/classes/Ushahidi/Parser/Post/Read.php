<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Ushahidi Read Post Parser
 *
 * @author Ushahidi Team <team@ushahidi.com>
 * @package Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;
use Ushahidi\Usecase\Post\ReadPostData;

class Ushahidi_Parser_Post_Read implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('id', array(
				['digit'],
				[
					function (Validation $valid, $field, $data) {
						if (empty($data['locale']) AND empty($data['id']))
						{
							$valid->error($field, 'emptyIdAndLocale');
						}
					},
					[':validation', ':field', ':data']
				]
			))
			->rules('parent_id', array(
				['digit'],
				[
					function (Validation $valid, $field, $data) {
						if (!  empty($data['locale']) AND empty($data['parent_id']))
						{
							$valid->error($field, 'emptyParentWithLocale');
						}
					},
					[':validation', ':field', ':data']
				]
			));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse post read request", $valid->errors('post'));
		}

		return new ReadPostData($data, Arr::extract($data, ['id', 'locale', 'parent_id']));
	}
}