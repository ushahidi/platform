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
use Ushahidi\Core\Session;

class ImportPosts
{
    protected $session;
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function handle($id, Entity $entity)
    {
        $userId = $this->session->getUser()->getId();
        dispatch(new ImportPostsJob($id, $userId));
    }
}
