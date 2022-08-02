<?php

/**
 * Ushahidi Platform Post Delete Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Usecase\Post;

use Ushahidi\App\V3\Usecase\DeleteUsecase;
use Ushahidi\App\V3\Usecase\Post\Concerns\FindPost;

class DeletePost extends DeleteUsecase
{
    use FindPost;
}
