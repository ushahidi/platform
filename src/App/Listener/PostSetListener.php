<?php

/**
 * Ushahidi PostSet Listener
 *
 * Listens for new posts that are added to a set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Listener;

use Ushahidi\Core\Entity\NotificationQueueRepository;

class PostSetListener
{
    protected $repo;

    public function __construct(NotificationQueueRepository $repo)
    {
        $this->repo = $repo;
    }

    public function handle(int $set_id, int $post_id)
    {
        // Insert into Notification Queue
        $state = [
            'set'  => $set_id,
            'post' => $post_id
        ];

        $entity = $this->repo->getEntity();
        $entity->setState($state);
        $this->repo->create($entity);
    }
}
