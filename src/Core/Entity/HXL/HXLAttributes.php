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

class HXLAttributes extends StaticEntity
{
	protected $id;
	protected $attribute;
	protected $tag_id;
	protected $description;

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
			'id'		=> 'int',
			'attribute' => 'string',
			'tag_id' 	=> 'int',
			'description' => 'string',
		];
	}

	// Entity
	public function getResource()
	{
		return 'hxl_attributes';
	}

	// StatefulData
	protected function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['tag_id']);
	}
}
