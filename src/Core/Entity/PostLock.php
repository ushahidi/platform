<?php
/**
 * Ushahidi Post Locks Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class PostLock extends StaticEntity
{
	protected $id;
	protected $user_id;
	protected $post_id;
	protected $expires;
	// StatefulData
	protected function getDerived()
	{
		// Foreign key alias
		return [
			'user_id' => ['user', 'user.id'],
            'post_id' => ['post', 'post.id'],
		];
	}
	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            	=> 'int',
			'user'          	=> false,
			'user_id'       	=> 'int',
			'post'          	=> false,
			'post_id'       	=> 'int',
			'expires'       	=> 'int',
		];
	}
	// Entity
	public function getResource()
	{
		return 'post_locks';
	}
	// StatefulData
	protected function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['user_id', 'post_id']);
	}
}
