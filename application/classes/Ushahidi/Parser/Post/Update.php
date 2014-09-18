<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Update Post Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;
use Ushahidi\Usecase\Post\PostData;

class Ushahidi_Parser_Post_Update implements Parser
{
	public function __invoke(Array $data)
	{
		// @todo should this be here? or only create?
		if (empty($data['slug']) AND !empty($data['title']))
		{
			$data['slug'] = URL::title(trim($data['title']));
		}

		if (! empty($data['locale']))
		{
			$data['locale'] = UTF8::strtolower(trim($data['locale']));
		}

		// Unpack form to get form_id
		if (isset($data['form']))
		{
			if (is_array($data['form']) AND isset($data['form']['id']))
			{
				$data['form_id'] = $data['form']['id'];
			}
			elseif (! is_array($data['form']))
			{
				$data['form_id'] = $data['form'];
			}
		}

		// Unpack possible user formats into 3 distinct values
		if (isset($data['user']) AND ! is_array($data['user']))
		{
			$data['user_id'] = $data['user'];
		}
		if (!empty($data['user']['id']))
		{
			$data['user_id'] = $data['user']['id'];
		}

		// Parse tags
		if (isset($data['tags']))
		{
			$data['tags'] = $this->parse_tags($data['tags']);
		}

		// Parse values
		if (isset($data['values']))
		{
			$data['values'] = $this->parse_values($data['values']);
		}

		$valid = Validation::factory($data)
			->rules('slug', array(
					array('not_empty'),
				))
			->rules('locale', array(
					array('not_empty'),
				))
			->rules('form_id', array(
					array('not_empty'),
				));


		if (!$valid->check())
		{
			throw new ParserException("Failed to parse post create request", $valid->errors('tag'));
		}

		// Ensure that all properties of a Post entity are defined by using Arr::extract
		return new PostData(
				Arr::extract($data, ['id', 'form_id', 'parent_id', 'title', 'content', 'status', 'slug', 'locale', 'user_id', 'author_email', 'author_realname'])
				+ Arr::extract($data, ['values', 'tags'], [])
			);
	}

	/**
	 * Flatten tags to just an array of tags/ids
	 * @param  array $tags incoming tags object
	 * @return array
	 */
	protected function parse_tags($tags)
	{
		// Flatten tags
		$new_tags = [];
		foreach ($tags as $value)
		{
			// Handle multiple formats
			// ID + URL array
			if (is_array($value) AND isset($value['id']))
			{
				$new_tags[] = $value['id'];
			}
			// Just ID or tag name
			else
			{
				$new_tags[] = $value;
			}
		}

		return $new_tags;
	}

	/**
	 * Parse and normalize post values
	 * @param  array  $values Incoming values object
	 * @return array          Normalized values object
	 */
	protected function parse_values($values)
	{
		// Just normalize value formats
		// to [
		// 	{
		// 		id : 1,
		// 		value : 'value1'
		// 	},
		// 	{
		// 		value : 'value2'
		// 	}
		// ]
		$_values = [];
		if (is_array($values))
		{
			foreach ($values as $key => $value)
			{
				// Skip null/empty values
				if (empty($value))
				{
					continue;
				}

				// Single simple value
				// { key : 'value' }
				if (!is_array($value))
				{
					$_values[$key] = [['value' => $value]];
				}
				// Single complex value key :
				// { 'lat': 1, 'lon' : 1 }
				// Does the array have string keys ? (ie. object hash not simple array)
				elseif ((bool) count(array_filter(array_keys($value), 'is_string')))
				{
					$_values[$key] = [['value' => $value]];
				}
				// Multivalue
				// [
				//   {'value' : 'value1', 'id' : 1},
				//   {'value' : 'value2'}
				// ]
				else
				{
					// Filter out empty values
					$_values[$key] = array_filter($value, function ($v) {
						// Make sure its an array with at least a 'value'
						return (is_array($v) && ! empty($v['value']));
					});
				}
			}
		}

		return $_values;
	}
}
