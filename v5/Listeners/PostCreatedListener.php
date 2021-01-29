<?php
/**
 * *
 *  * Ushahidi Acl
 *  *
 *  * @author     Ushahidi Team <team@ushahidi.com>
 *  * @package    Ushahidi\Application
 *  * @copyright  2020 Ushahidi
 *  * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 *
 *
 */

namespace v5\Listeners;

use v5\Events\PostCreatedEvent;

use League\Event\EventInterface;
use Ushahidi\App\Listener\PostListener;
use Ushahidi\Core\Traits\Events\DispatchesEvents;
use v5\Models\Post\Post;

class PostCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderShipped  $event
     * @return void
     */
    public function handle(PostCreatedEvent $event)
    {

        $state = [
            'post_id' => $event->post->id,
            'event_type' => 'create'
        ];
        $webhookRepo = service('repository.webhook.job');

        $entity = $webhookRepo->getEntity();
        $entity->setState($state);
        $webhookRepo->create($entity);
    }
}
