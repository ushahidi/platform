<?php

/**
 * Add post to Set Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Usecase\Set;

use Ushahidi\Contracts\Entity;
use Ushahidi\App\V3\Usecase\CreateUsecase;
use Ushahidi\App\V3\Usecase\Concerns\VerifyEntityLoaded;

class CreateSet extends CreateUsecase
{
    use AuthorizeSet, VerifyEntityLoaded;

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
