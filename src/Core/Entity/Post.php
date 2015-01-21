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
	protected $values;
	protected $tags;

	// StatefulData
	protected function getDerived()
	{
		return [
			'slug'    => 'title',
			'form_id' => ['form', 'form.id'], /* alias */
			'user_id' => ['user', 'user.id'], /* alias */
		];
	}

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'              => 'int',
			'parent_id'       => 'int',
			'form'            => false, /* alias */
			'form_id'         => 'int',
			'user'            => false, /* alias */
			'user_id'         => 'int',
			'type'            => 'string',
			'title'           => 'string',
			'slug'            => '*slug',
			'content'         => 'string',
			'author_email'    => 'string', /* @todo email filter */
			'author_realname' => 'string', /* @todo redundent with user record */
			'status'          => 'string',
			'created'         => 'int',
			'updated'         => 'int',
			'locale'          => '*lowercasestring',
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
