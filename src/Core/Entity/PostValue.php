<?php

/**
 * Ushahidi Post Values
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class PostValue extends StaticEntity
{
	protected $id;
	protected $post_id;
	protected $form_attribute_id;
	protected $value;
	protected $created;
	protected $media;
	// Attribute fields
	protected $key;
	protected $cardinality;
	protected $type;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'                => 'int',
			'post_id'           => 'int',
			'form_attribute_id' => 'int',
			'value'             => null, /* @todo array or string? not sure */
			'created'           => 'int',
			'key'               => 'string',
			'cardinality'       => 'int',
			'type'              => 'string',
		];
	}

	// Entity
	public function getResource()
	{
		return 'post_values';
	}
}
