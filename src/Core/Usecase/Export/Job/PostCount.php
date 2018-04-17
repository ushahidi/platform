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
use Ushahidi\Core\Traits\UserContext;
class PostCount extends ReadUsecase
{

	public function setUserRepo(\Ushahidi\Core\Entity\UserRepository $repo)
	{
		$this->userRepository = $repo;
	}

	public function interact()
	{
        $entity = $this->getEntity();
		/**
		 * Enables user contexts when outside of an oauth request
		 * for the user selected in the export job
		 */
		$userContextService = service('usercontext.service');
        $user = $this->userRepository->get($entity->user_id);
		$userContextService->setUser($user);
        return $this->repo->getPostCount($entity->id);
    }
}
