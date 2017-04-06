<?php

/**
 * Ushahidi Webhook Job
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class WebhookJob extends StaticEntity
{
	protected $id;
	protected $post_id;
	protected $event_type;
	protected $created;

	// StatefulData
	protected function getDerived()
	{
		// Foreign key alias
		return [
			'post_id' => ['post', 'post.id']
		];
	}


	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'       			=> 'int',
			'post'     			=> false,
			'post_id'  			=> 'int',
			'event_type'	  => 'string',
			'created' 			=> 'int'
		];
	}

	// Entity
	public function getResource()
	{
		return 'webhook_job';
	}

	// StatefulData
	protected function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['post_id']);
	}
}
