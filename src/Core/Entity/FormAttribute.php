<?php

/**
 * Ushahidi Form Attribute
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class FormAttribute extends StaticEntity
{
	protected $id;
	protected $key;
	protected $label;
	protected $input;
	protected $type;
	protected $required;
	protected $default;
	protected $priority;
	protected $options = [];
	protected $cardinality;
	protected $form_stage_id;

	// StatefulData
	protected function getDerived()
	{
		return [
			'form_stage_id' => ['form_stage', 'form_stage.id'], /* alias */
			'key'    => function ($data) {
				if (array_key_exists('label', $data)) {
					return $data['label'] . ' ' . uniqid();
				}
				return false;
			},
		];
	}

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            => 'int',
			'key'           => '*slug',
			'label'         => 'string',
			'input'         => 'string',
			'type'          => 'string',
			'required'      => 'bool',
			'default'       => 'string',
			'priority'      => 'int',
			'options'       => '*json',
			'cardinality'   => 'int',
			'form_stage'    => false, /* alias */
			'form_stage_id' => 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'form_attributes';
	}
}
