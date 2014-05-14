<?php

/**
 * Ushahidi Post
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class Post extends Entity
{
	public $id;
	public $parent_id;
	public $form_id;
	public $user_id;
	public $type;
	public $title;
	public $slug;
	public $content;
	public $status;
	public $created;
	public $updated;
	public $locale;

	public function getResource()
	{
		return 'posts';
	}
}

