<?php

/**
 * Ushahidi Set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Set extends StaticEntity
{
	protected $id;
	protected $user_id;
	protected $name;
	protected $description;
	protected $url;
	protected $view;
	protected $view_options;
	protected $visible_to;
	protected $featured;
	protected $created;
	protected $updated;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'           => 'int',
			'user_id'      => 'int',
			'name'         => 'string',
			'description'  => 'string',
			'url'          => '*url',
			'view'         => 'string',
			'view_options' => '*json',
			'visible_to'   => '*json',
			'featured'     => 'boolean',
			'created'      => 'int',
			'updated'      => 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'sets';
	}

	// StatefulData
	protected function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['user_id']);
	}

	// StatefulData
	protected function getDerived()
	{
		return [
			'user_id'   => ['user', 'user.id'], /* alias */
		];
	}
}
