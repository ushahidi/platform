<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter
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

class Ushahidi_Formatter_API implements Formatter
{
	// Formatter
	public function __invoke($entity)
	{
		if (!($entity instanceof Entity))
			throw new FormatterException("API formatter requries an Entity as input");

		$fields = $entity->asArray();

		$data = [
			'id'  => $entity->id,
			'url' => URL::site(Ushahidi_Api::url($entity->getResource(), $entity->id), Request::current()),
			];

		if (array_key_exists('parent_id', $fields))
		{
			$data['parent'] = $this->get_relation($entity->getResource(), $entity->parent_id);
			unset($fields['parent_id']);
		}

		if (array_key_exists('user_id', $fields))
		{
			$data['user'] = $this->get_relation('users', $entity->user_id);
			unset($fields['user_id']);
		}

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

	/**
	 * Format relations into url/id arrays
	 * @param  string $resource resource name as used in urls
	 * @param  int    $id       resource id
	 * @return array
	 */
	protected function get_relation($resource, $id)
	{
		return !$id ? NULL : [
			'id'  => $id,
			'url' => URL::site(Ushahidi_Api::url($resource, $id), Request::current()),
		];
	}
}
