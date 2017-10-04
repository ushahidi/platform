<?php

/**
 * Ushahidi Webhook Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Webhook extends StaticEntity
{
	protected $id;
	protected $user_id;
	protected $url;
	protected $name;
	protected $shared_secret;
	protected $event_type;
	protected $entity_type;
	protected $created;
	protected $updated;

	// StatefulData
	protected function getDerived()
	{
		// Foreign key alias
		return [
			'user_id' => ['user', 'user.id']
		];
	}

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            	=> 'int',
			'name'					  => 'string',
			'url'							=> 'string',
			'shared_secret'		=> 'string',
			'event_type'		  => 'string',
			'entity_type'		  => 'string',
			'user'          	=> false,
			'user_id'       	=> 'int',
			'created'       	=> 'int',
			'updated'       	=> 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'webhooks';
	}

	// StatefulData
	protected function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['user_id']);
	}
}
