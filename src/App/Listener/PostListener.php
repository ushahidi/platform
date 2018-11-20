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

use Ushahidi\Core\Entity\WebhookJobRepository;
use Ushahidi\Core\Entity\WebhookRepository;
use Log;

class PostListener
{
    protected $allowed_events = ['create', 'update'];

    protected $repo;
    protected $webhook_repo;

    public function __construct(WebhookJobRepository $repo, WebhookRepository $webhook_repo)
    {
        $this->repo = $repo;
        $this->webhook_repo = $webhook_repo;
    }

    public function handle($eventName, $payload)
    {
        $post_id = $payload['id'];

        $event_type = str_replace('posts.', '', $eventName);

        if (!in_array($event_type, $this->allowed_events)) {
            return;
        }

        $state = [
            'post_id' => $post_id,
            'event_type' => $event_type
        ];

        $entity = $this->repo->getEntity();
        $entity->setState($state);
        $this->repo->create($entity);
    }
}
