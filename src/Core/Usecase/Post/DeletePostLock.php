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

class DeletePostLock extends DeleteUsecase
{
    use PostLockTrait;
    // Usecase
	public function interact()
	{
		// Fetch a default entity and apply the payload...
		$post = $this->getPostEntity();

		// ... verify that the entity can be created by the current user
		$this->verifyLockAuth($post);

		if ($this->getIdentifier('lock_id')) {
            $id = $this->repo->releaseLockByLockId($this->getIdentifier('lock_id'));
        } else {
            $id = $this->repo->releaseLock($post->id);
        }

        $lock = $this->getLockEntity($id);

        return $this->formatter->__invoke($lock);
	}
}
