<?php

/**
 * Ushahidi Notifications Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Illuminate\Console\Command;

use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Entity\NotificationQueueRepository;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\App\DataSource\DataSourceManager;
use Ushahidi\App\Multisite\OhanzeeResolver;
use Ushahidi\App\Multisite\UsesSiteInfo;

class Notification extends Command
{
    use UsesSiteInfo;

    private $postRepository;
    private $contactRepository;
    private $messageRepository;
    private $notificationQueueRepository;
    protected $resolver;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notification:queue';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'notification:queue {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue notifications for sending';

    public function __construct(DataSourceManager $sources, OhanzeeResolver $resolver)
    {
        parent::__construct();

        $this->sources = $sources;
        $this->resolver = $resolver;
    }

    /**
     * Get current connection
     *
     * @return Ohanzee\Database;
     */
    protected function db()
    {
        return $this->resolver->connection();
    }

    public function handle()
    {
        $this->contactRepository = service('repository.contact');
        $this->postRepository = service('repository.post');
        $this->messageRepository = service('repository.message');
        $this->notificationQueueRepository = service('repository.notification.queue');

        $limit = $this->option('limit');

        $count = 0;

        // Get Queued notifications
        $notifications = $this->notificationQueueRepository->getNotifications($limit);

        // Start transaction
        $this->db()->begin();

        foreach ($notifications as $notification) {
            // Get contacts and generate messages from new notification
            $count+=$this->generateMessages($notification);
        }

        // Finally commit changes
        $this->db()->commit();

        $this->info("{$count} messages queued for sending");
    }

    private function generateMessages($notification)
    {
        $this->info("Generating messages for post {$notification->post_id} in set {$notification->set_id}");

        // Delete queued notification
        $this->notificationQueueRepository->delete($notification);

        // Get post title and text
        $post = $this->postRepository->get($notification->post_id);

        $count = 0;

        $offset = 0;
        $limit = 1000;

        $site_name = $this->getSite()->getName() ?: 'Ushahidi';
        $client_url = $this->getSite()->getClientUri();

        // Get contacts (max $limit at a time) and generate messages.
        while (true) {
            $contacts = $this->contactRepository
                ->getNotificationContacts($notification->set_id, $limit, $offset);
            $countContacts = count($contacts);

            $this->info("Got $countContacts contacts to notify about set {$notification->set_id}");

            // Create outgoing messages
            foreach ($contacts as $contact) {
                if ($this->messageRepository->notificationMessageExists($post->id, $contact->id)) {
                    $this->info("Contact {$contact->id} already notified");
                    continue;
                }

                $subs = [
                    'sitename' => $site_name,
                    'title' => $post->title,
                    'content' => $post->content,
                    'url' => $client_url . '/posts/' . $post->id
                ];

                $messageType = $this->mapContactToMessageType($contact->type);
                $data_source = null;
                if ($contact->data_source) {
                    $data_source = $contact->data_source;
                } elseif ($source_service = $this->sources->getSourceForType($messageType)) {
                    $data_source = $source_service->getId();
                }

                $state = [
                    'contact_id' => $contact->id,
                    'notification_post_id' => $post->id,
                    'title' => trans('notifications.' . $messageType . '.title', $subs),
                    'message' => trans('notifications.' . $messageType . '.message', $subs),
                    'type' => $messageType,
                    'data_source' => $data_source,
                    'direction' => Message::OUTGOING
                ];

                $entity = $this->messageRepository->getEntity();
                $entity->setState($state);
                $id = $this->messageRepository->create($entity);

                $count++;
                $this->info("Queued message id {$id} for {$contact->id}");
            }

            if ($countContacts < $limit) {
                $this->info('Ran out of contacts');
                break;
            }

            $offset+=$limit;
        }

        return $count;
    }


    private $contactToMessageTypeMap = [
        'phone' => 'sms',
        'email' => 'email',
        'twitter' => 'twitter',
    ];

    private function mapContactToMessageType($contactType)
    {
        return isset($this->contactToMessageTypeMap[$contactType])
            ? $this->contactToMessageTypeMap[$contactType] : $contactType;
    }
}
