<?php

/**
 * Ushahidi Platform Create Post Lock Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\UpdateUsecase;
use Ushahidi\Core\Data;

class CreatePostLock extends UpdateUsecase
{
	use PostLockTrait;

    // Usecase
	public function interact()
	{
		// Fetch a default entity and apply the payload...
		$post = $this->getPostEntity();
		
		// ... verify that the entity can be created by the current user
		$this->verifyLockAuth($post);
		
		$id = $this->repo->getLock($post);
		
		$lock = $this->getLockEntity($id);
		
		return $this->formatter->__invoke($lock);
	}
}
