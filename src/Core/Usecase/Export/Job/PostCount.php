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
	use UserContext;

	public function interact()
	{
		$entity = $this->getEntity();
		$this->getSession()->setUser($entity->user_id);
		return $this->repo->getPostCount($entity->id);
	}
}
