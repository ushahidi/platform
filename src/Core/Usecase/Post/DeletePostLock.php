<?php

/**
 * Ushahidi Platform Break Post Lock Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\DeleteUsecase;
use Ushahidi\Core\Data;
use Ushahidi\Core\Entity\PostLock;
use Ushahidi\Core\Traits\UserContext;

class DeletePostLock extends DeleteUsecase
{
    // Provides getUser()
	use UserContext;

    use PostLockTrait;

    // Usecase
	public function interact()
	{
		// Fetch a default entity and apply the payload...
		$post = $this->getPostEntity();
        $lock = new PostLock();

		// ... verify that the entity can be locked by the current user
		$this->verifyLockAuth($post);

        // We have 3 mechanisms by which a lock(s) can be release
        // by lock id, by post id or by user id
        // In the case of user id we release all locks owned by this user
		if ($this->getIdentifier('lock_id')) {
            $lock = $this->repo->releaseLockByLockId($this->getIdentifier('lock_id'));
        } elseif ($this->getIdentifier('post_id')) {
            $lock = $this->repo->releaseLock($post->id);
        } else {
            $user = $this->getUser();
            if ($user->id) {
                $this->repo->releaseLocksByUserId($user->id);
            }
        }

        return $this->formatter->__invoke($lock);
	}
}
