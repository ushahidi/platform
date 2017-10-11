<?php

/**
 * Ushahidi Platform Create Post Changelog Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;

class CreatePostChangelog extends CreateUsecase
{
	// - VerifyStageLoaded for checking that the post exists
	use VerifyPostLoaded;

	// For post check:
	// - IdentifyRecords
	// - VerifyEntityLoaded
	use IdentifyRecords,
		VerifyEntityLoaded;

	// CreateUsecase
	protected function getEntity()
	{
		$entity = parent::getEntity();

		$this->verifyPostExists($entity);

		return $entity;
	}
}
