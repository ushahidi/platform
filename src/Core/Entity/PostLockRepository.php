<?php

/**
 * Repository for Post Lock
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;

interface PostLockRepository extends
	EntityGet
{
    public function releaseLock($entity_id);

    public function releaseLockByLockId($lock_id);

    public function isActive($entity_id);

    public function getPostLock($entity_id);
}
