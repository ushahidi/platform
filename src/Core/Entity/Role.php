<?php

/**
 * Ushahidi Role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Role extends StaticEntity
{
	protected $name;
	protected $display_name;
	protected $description;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'name'         => 'string',
			'display_name' => 'string',
			'description'  => 'string',
		];
	}

	// Entity
	public function getResource()
	{
		return 'roles';
	}

	// Entity
	public function getId()
	{
		return $this->name;
	}

	// StatefulData
	protected function getImmutable()
	{
		return ['name'];
	}
}
