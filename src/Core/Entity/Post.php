<?php

/**
 * Ushahidi Post
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Post extends StaticEntity
{
	protected $id;
	protected $parent_id;
	protected $form_id;
	protected $user_id;
	protected $type;
	protected $title;
	protected $slug;
	protected $content;
	protected $author_email;
	protected $author_realname;
	protected $status;
	protected $created;
	protected $updated;
	protected $locale;
	protected $values = [];
	protected $tags = [];

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'              => 'int',
			'parent_id'       => 'int',
			'form_id'         => 'int',
			'user_id'         => 'int',
			'type'            => 'string',
			'title'           => 'string',
			'slug'            => 'string',
			'content'         => 'string',
			'author_email'    => 'string', /* @todo email filter */
			'author_realname' => 'string', /* @todo redundent with user record */
			'status'          => 'string',
			'created'         => 'int',
			'updated'         => 'int',
			'locale'          => 'string',
			'values'          => 'array',
			'tags'            => 'array',
		];
	}

	// Entity
	public function getResource()
	{
		return 'posts';
	}
}
