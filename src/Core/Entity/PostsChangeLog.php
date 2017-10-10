<?php

/**
 * Ushahidi Tag
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class PostsChangeLog extends StaticEntity
{
	protected $id;
	protected $user_id;
	protected $created;
	protected $post_id;
	protected $change_type;
	protected $item_changed;
	protected $content;
	protected $entry_type;

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
			'id'         => 'int',
			'user_id'    => 'int',
			'created'    => 'int',
			'post_id'		 => 'int',
			'change_type' =>	'string',
			'item_changed' =>	'string',
			'entry_type' =>	'string',
			'content' =>	'string',
		];
	}

	// Entity
	public function getResource()
	{
		return 'posts_changelog';
	}


}
