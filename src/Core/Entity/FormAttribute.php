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
	// @todo move this. priority is really on a property of an attribute *in* a group
	protected $priority;
	protected $options = [];
	protected $cardinality;
	protected $form_group_id;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            => 'int',
			'key'           => 'string',
			'label'         => 'string',
			'input'         => 'string',
			'type'          => 'string',
			'required'      => 'bool',
			'default'       => 'string',
			'priority'      => 'int',
			'options'       => 'array',
			'cardinality'   => 'int',
			'form_group_id' => 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'form_attributes';
	}
}
