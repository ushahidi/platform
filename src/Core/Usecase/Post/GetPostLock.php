<?php

/**
 * Ushahidi Platform Get Post Lock Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\LockUsecase;

class GetPostLock extends LockUsecase
{

    // Usecase
	public function interact()
	{
        $entity = $this->getEntity();

        //$this->verifyLockAuth($entity);

        $result = $this->repo->getLock($entity);

        return $this->formatter->__invoke($result);
    }
}
