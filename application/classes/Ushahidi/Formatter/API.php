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

use Ushahidi\Entity;
use Ushahidi\Tool\Formatter;
use Ushahidi\Exception\FormatterException;

class Ushahidi_Formatter_API implements Formatter
{
	/**
	 * @var array $timestamps fields to be treated as timestamps
	 */
	protected $timestamps = array('created', 'updated');

	// Formatter
	public function __invoke($entity)
	{
		if (!($entity instanceof Entity))
			throw new FormatterException("API formatter requries an Entity as input");

		$fields = array_keys($entity->asArray());
		$fields = array_combine($fields, $fields);

		$data = [
			'id'  => $entity->id,
			'url' => URL::site(Ushahidi_Api::url($entity->getResource(), $entity->id), Request::current()),
			];

		if (isset($fields['parent_id']))
		{
			$data['parent'] = !$entity->parent_id ? NULL : [
				'id'  => $entity->parent_id,
				'url' => URL::site(Ushahidi_Api::url($entity->getResource(), $entity->parent_id), Request::current()),
			];
			unset($fields['parent_id']);
		}

		foreach ($fields as $field)
		{
			if (in_array($field, $this->timestamps))
			{
				$data[$field] = date(DateTime::W3C, $entity->$field);
			}
			else
			{
				$data[$field] = $entity->$field;
			}
		}

		return $data;
	}

}

