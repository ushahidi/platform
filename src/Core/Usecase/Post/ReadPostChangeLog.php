<?php

/**
 * Read post in Set Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\Post\ReadPost;

class ReadPostChangeLog extends ReadPost
{
	use ChangelogTrait;
    // Usecase
  	public function interact()
  	{

			//TODO: this is what needs to change, I think...
			// load the corresponding changelog collection and display its attributes here

  		$post_entity = $this->getPostEntity();

			$entry_id     = $this->getIdentifier('entry_id');
			$post_id = $this->getIdentifier('post_id');
			$changelog_obj = $this->getFullChangelog($post_id);

			// ... and return the formatted result.
			//TODO: use formatter
			return $changelog_obj;
  	}
}
