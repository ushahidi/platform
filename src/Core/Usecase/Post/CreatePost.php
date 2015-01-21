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
	protected function getEntity()
	{
		$payload = $this->payload;

		// If no user information is provided, default to the current session user.
		if (
			empty($payload['user']) &&
			empty($payload['user_id']) &&
			empty($payload['author_email']) &&
			empty($payload['author_realname']) &&
			$this->auth->getUserId()
		) {
			$payload['user_id'] = $this->auth->getUserId();
		}

		return $this->repo->getEntity()->setState($payload);
	}

	protected function verifyReadAuth(Entity $entity)
	{
		// Throwing an error w/o read permissions breaks on anonymous users
		// Maybe should just return a 204 (No Content)? or 202 (Accepted)?
		// $this->verifyAuth($entity, 'read');
	}
}
