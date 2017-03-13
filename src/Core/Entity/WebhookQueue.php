<?php

/**
 * Ushahidi Webhook Queue
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class WebhookQueue extends StaticEntity
{
	protected $id;
	protected $post_id;
	protected $webhook_id;
	protected $created;

	// StatefulData
	protected function getDerived()
	{
		// Foreign key alias
		return [
			'post_id' => ['post', 'post.id'],
			'webhook_id'  => ['webhook', 'webhook.id']
		];
	}


	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'       			=> 'int',
			'post'     			=> false,
			'post_id'  			=> 'int',
			'webhook'      => false,
			'webhook_id'	  => 'string',
			'created' 			=> 'int'
		];
	}

	// Entity
	public function getResource()
	{
		return 'webhook_queue';
	}
}
