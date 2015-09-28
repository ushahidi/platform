<?php

/**
 * Ushahidi Notification Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Notification extends StaticEntity
{
	protected $id;
	protected $user_id;
	protected $set_id;
	protected $created;
	protected $updated;

	// StatefulData
	protected function getDerived()
	{
		// Foreign key alias
		return [
			'user_id' => ['user', 'user.id'],
			'set_id'  => ['set', 'set.id']
		];
	}

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            => 'int',
			'user'          => false,
			'user_id'       => 'int',
			'set'           => false,
			'set_id'        => 'int',
			'created'       => 'int',
			'updated'       => 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'notifications';
	}

	// StatefulData
	protected function getImmutable()
	{
		return ['id', 'user_id', 'set_id', 'created'];
	}
}
