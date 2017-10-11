<?php

/**
 * Ushahidi Platform Search Post Changelog Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\SearchUsecase;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;

class SearchPostChangelog extends SearchUsecase
{
	// - VerifyPostLoaded for checking that the post exists
	use VerifyPostLoaded;

	// For post check:
	// - IdentifyRecords
	// - VerifyEntityLoaded
	use IdentifyRecords,
		VerifyEntityLoaded;

			protected function verifyPostExists()
			{
				\Log::instance()->add(\Log::INFO, 'In Verify ');

				if ($identifier = $this->getIdentifier('post_id')) {
					$post = $this->post_repo->get($identifier);
					$this->verifyEntityLoaded($post, $this->identifiers);
				}
			}

}
