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
	protected $contact_id;
	protected $set_id;
	protected $is_subscribed;
	protected $created;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            => 'int',
			'contact_id'    => 'int',
			'set_id'        => 'int',
			'is_subscribed' => 'int',
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
		return ['id', 'contact_id', 'set_id', 'created'];
	}
}
