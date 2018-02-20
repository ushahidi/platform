<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for CSV
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_CSV extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;

	// Formatter
	public function __invoke($entity)
	{
		if (!($entity instanceof Entity))
			throw new FormatterException("API formatter requries an Entity as input");

		$fields = $entity->asArray();

		$data = [
			'id'  => $entity->id,
			'url' => URL::site(Ushahidi_Rest::url($entity->getResource(), $entity->id), Request::current()),
		];

		if (isset($fields['parent_id']))
		{
			$data['parent'] = $this->get_relation($entity->getResource(), $entity->parent_id);
			unset($fields['parent_id']);
		}

		if (isset($fields['user_id']))
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
}
