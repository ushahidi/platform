<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Post
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Formatter;

class Ushahidi_Formatter_Post extends Ushahidi_Formatter_API
{

	public function __construct(Formatter $value_formatter)
	{
		$this->value_formatter = $value_formatter;
	}

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
		$value_formatter = $this->value_formatter;

		$values_with_keys = [];
		foreach($values as $value)
		{
			$key = $value->key;
			$cardinality = $value->cardinality;
			$formatted_value = $value_formatter($value);

			if (! isset($values_with_keys[$key]))
			{
				$values_with_keys[$key] = [];
			}
			// Save value and id in multi-value format.
			$values_with_keys[$key][] = $formatted_value;

			// First or single value for attribute
			if (! isset($output[$key]) AND $cardinality == 1)
			{
				$output[$key] = $formatted_value['value'];
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
