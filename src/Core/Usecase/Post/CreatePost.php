<?php

/**
 * Ushahidi Platform Entity Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreatePost extends CreateUsecase
{
	// - VerifyParentLoaded for checking that the parent exists
	use VerifyParentLoaded;

	protected function getEntity()
	{
		$entity = parent::getEntity();

		// If no user information is provided, default to the current session user.
		if (empty($entity->user_id) &&
			empty($entity->author_email) &&
			empty($entity->author_realname) &&
			$this->auth->getUserId()
		) {
			$entity->setState(['user_id' => $this->auth->getUserId()]);
		}

		// If status is not set..
		if (empty($entity->status)) {
			// .. check if the post requires approval
			// .. and set a default status
			if ($this->repo->doesPostRequireApproval($entity->form_id)) {
				$entity->setState(['status' => 'draft']);
			} else {
				$entity->setState(['status' => 'published']);
			}
		}

		return $entity;
	}
}
