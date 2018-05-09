<?php

/**
 * Ushahidi HXLTag Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity\HXL;

use Ushahidi\Core\StaticEntity;

class HXLTagAttributes extends StaticEntity
{
	protected $id;
	protected $form_attribute_type;
	protected $hxl_tag_id;
	protected $hxl_tag;

	protected function getDerived()
	{
		// Foreign key alias
		return [
			'hxl_tag_id' => ['hxl_tags', 'hxl_tags.id'],
		];
	}

	// DataTransformer
	public function getDefinition()
	{
		return [
			'id' => 'int',
			'form_attribute_type' => 'string',
			'hxl_tag_id' => 'int',
			'hxl_tag' => 'array'
		];
	}

	// Entity
	public function getResource()
	{
		return 'hxl_attribute_type_tag';
	}


	// StatefulData
	protected function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['hxl_tag_id']);
	}
}
