<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Post
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Formatter_Post extends Ushahidi_Formatter_API
{

	protected function get_field_name($field)
	{
		$remap = [
			'form_id' => 'form',
			];

		if (isset($remap[$field])) {
			return $remap[$field];
		}

		return parent::get_field_name($field);
	}

	protected function format_form_id($form_id)
	{
		return $this->get_relation('forms', $form_id);
	}

	protected function format_tags($tags)
	{
		$output = [];
		foreach ($tags as $tagid)
		{
			$output[] = $this->get_relation('tags', $tagid);
		}

		return $output;
	}

	protected function format_values($values)
	{
		$output = [];

		// @todo custom formatting by type (ie. for points)
		$values_with_keys = [];
		foreach($values as $value)
		{
			$value = $value->asArray();

			$key = $value['key'];
			$cardinality = $value['cardinality'];

			if (! isset($values_with_keys[$key]))
			{
				$values_with_keys[$key] = [];
			}
			// Save value and id in multi-value format.
			$values_with_keys[$key][] = [
				'id' => $value['id'],
				'value' => $value['value']
			];

			// First or single value for attribute
			if (! isset($output[$key]) AND $cardinality == 1)
			{
				$output[$key] = $value['value'];
			}
			// Multivalue - use array instead
			else
			{
				$output[$key] = $values_with_keys[$key];
			}
		}

		return $output;
	}

}
