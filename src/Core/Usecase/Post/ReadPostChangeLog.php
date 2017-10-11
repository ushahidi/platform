<?php

/**
 * Ushahidi Platform Read Post Changelog Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\ReadUsecase;

class ReadPostChangelog extends ReadUsecase
{
	// - VerifyPostLoaded for checking that the post exists
	use VerifyPostLoaded;
}
