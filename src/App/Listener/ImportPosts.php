<?php
namespace Ushahidi\App\Listener;

/**
 * Ushahidi Import Posts Listener
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\App\Facades\Features;
use Ushahidi\App\Jobs\ImportPostsJob;

class ImportPosts
{
    public function handle($id, Entity $entity)
    {
        dispatch(new ImportPostsJob($id));
    }
}
