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

namespace Ushahidi\Modules\V5\Listeners;

use Ushahidi\Modules\V5\Events\PostCreatedEvent;

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
     * @param  PostCreatedEvent  $event
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
