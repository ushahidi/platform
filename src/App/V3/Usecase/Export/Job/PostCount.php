<?php

/**
 * Ushahidi Platform Export Job Create Use Case
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Usecase\Export\Job;

use Ushahidi\App\V3\Usecase\ReadUsecase;
use Ushahidi\Core\Concerns\UserContext;

class PostCount extends ReadUsecase
{
    use UserContext;

    public function interact()
    {
        $entity = $this->getEntity();
        $this->getSession()->setUser($entity->user_id);
        return $this->repo->getPostCount($entity->id);
    }
}
