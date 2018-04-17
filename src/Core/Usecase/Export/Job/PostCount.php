<?php

/**
 * Ushahidi Platform Export Job Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Export\Job;

use Ushahidi\Core\Usecase\ReadUsecase;

class PostCount extends ReadUsecase
{
    public function interact()
	{
        $entity = $this->getEntity();
        return $this->repo->getPostCount($entity->id);
    }
}
