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
	protected $form_id;
	protected $user_id;
	protected $url;
	protected $name;
	protected $shared_secret;
	protected $webhook_uuid;
	protected $event_type;
	protected $entity_type;
	protected $source_field_key;
	protected $destination_field_key;
	protected $created;
	protected $updated;

	// StatefulData
	protected function getDerived()
	{
		// Foreign key alias
		return [
			'user_id' => ['user', 'user.id'],
			'form_id'   => ['form', 'form.id']
		];
	}

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            	        => 'int',
			'name'					          => 'string',
			'url'							        => 'string',
			'shared_secret'		        => 'string',
			'webhook_uuid'		        => 'string',
			'event_type'		          => 'string',
			'entity_type'		          => 'string',
			'source_field_key'	      => 'string',
			'destination_field_key'   => 'string',
			'user'          	        => false,
			'user_id'       	        => 'int',
			'form'                    => false, /* alias */
			'form_id'                 => 'int',
			'created'       	        => 'int',
			'updated'       	        => 'int',
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
