<?php

/**
 * Ushahidi Platform Post Read Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Post;

use Ushahidi\Usecase\ReadUsecase;
use Ushahidi\Data;
use Ushahidi\Traits\FindPostEntity;

class ReadPost extends ReadUsecase
{
	use FindPostEntity;
}
