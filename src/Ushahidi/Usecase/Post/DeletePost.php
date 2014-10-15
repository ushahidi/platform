<?php

/**
 * Ushahidi Platform Post Delete Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Post;

use Ushahidi\Usecase\DeleteUsecase;
use Ushahidi\Data;

class DeletePost extends DeleteUsecase
{
	use FindPostEntity;
}
