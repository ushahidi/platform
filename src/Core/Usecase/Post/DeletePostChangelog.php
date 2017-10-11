<?php

/**
 * Ushahidi Platform Delete Post Changelog Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\DeleteUsecase;

class DeletePostChangelog extends DeleteUsecase
{
	// - VerifyPostLoaded for checking that the form exists
	use VerifyPostLoaded;
}
