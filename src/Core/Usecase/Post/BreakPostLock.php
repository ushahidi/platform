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

use Ushahidi\Core\Usecase\LockUsecase;

class BreakPostLock extends LockUsecase
{
    // Usecase
	public function interact()
	{
        $result = [];
        if ($this->getIdentifier('lock_id')) {
            $result = $this->repo->releaseLockByLockId($this->getIdentifier('lock_id'));
        } else {
            $entity = $this->getEntity();
            $result = $this->repo->releaseLock($entity->id);
        }
        //$this->verifyLockAuth($entity);

        

        return $this->formatter->__invoke($result);
    }
}
