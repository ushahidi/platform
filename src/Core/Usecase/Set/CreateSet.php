<?php

/**
 * Add post to Set Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreateSet extends CreateUsecase
{
	use
		VerifyEntityLoaded,
		AuthorizeSet;

	/**
	 * Find entity based on identifying parameters.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
        $entity = parent::getEntity();
        // always use the current session user.
        if ($this->auth->getUserId()) {
            $entity->setState(['user_id' => $this->auth->getUserId()]);
        }

        return $entity;
	}
}
