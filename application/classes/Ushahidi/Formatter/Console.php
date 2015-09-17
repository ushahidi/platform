<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Console Formatter
 *
 * Takes an entity object and returns an array.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Exception\FormatterException;

class Ushahidi_Formatter_Console implements Formatter
{
	// Formatter
	public function __invoke($entity)
	{
		if (!($entity instanceof Entity))
			throw new FormatterException("Console formatter requries an Entity as input");

		$fields = $entity->asArray();

		$data = [
			'id'  => $entity->id,
			];

		foreach ($fields as $field => $value)
		{
			$name = $this->get_field_name($field);
			if (is_string($value))
			{
				$value = trim($value);
			}

			$method = 'format_' . $field;
			if (method_exists($this, $method))
			{
				$data[$name] = $this->$method($value);
			}
			else
			{
				$data[$name] = $value;
			}
		}

		$data = $this->add_metadata($data, $entity);

		return $data;
	}

	/**
	 * Method that can add any kind of additional metadata about the entity,
	 * by overloading this method in an extended class.
	 *
	 * Must return the formatted data!
	 *
	 * @param  Array  $data   formatted data
	 * @param  Entity $entity resource
	 * @return Array
	 */
	protected function add_metadata(Array $data, Entity $entity)
	{
		// By default, noop
		return $data;
	}

	protected function get_field_name($field)
	{
		// can be overloaded to remap specific fields to different public names
		return $field;
	}

	protected function format_created($value)
	{
		return date(DateTime::W3C, $value);
	}

	protected function format_updated($value)
	{
		return $value ? $this->format_created($value) : NULL;
	}
}
