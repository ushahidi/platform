<?php

/**
 * Ushahidi Webhook Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Illuminate\Console\Command;

use Ushahidi\Core\Tool\Signer;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\WebhookJobRepository;
use Ushahidi\Core\Entity\WebhookRepository;

class Webhook extends Command
{
    private $db;
    private $postRepository;
    private $webhookRepository;
    private $webhookJobRepository;
    private $client;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'webhook:send';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'webhook:send {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send webhook requests';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->db = service('kohana.db');
        $this->webhookRepository = service('repository.webhook');
        $this->postRepository = service('repository.post');
        $this->webhookJobRepository = service('repository.webhook.job');

        $this->client = new \GuzzleHttp\Client();

        $limit = $this->option('limit');

        $count = 0;

        // Get Queued webhook requests
        $webhook_requests = $this->webhookJobRepository->getJobs($limit);

        // Start transaction
        $this->db->begin();

        foreach ($webhook_requests as $webhook_request) {
            $this->generateRequest($webhook_request);

            $count++;
        }

        // Finally commit changes
        $this->db->commit();

        $this->info("{$count} webhook requests sent");
    }

    private function generateRequest($webhook_request)
    {
        // Delete queued webhook request
        $this->webhookJobRepository->delete($webhook_request);

        // Get post data
        $post = $this->postRepository->get($webhook_request->post_id);

        // Get webhook data
        $webhooks = $this->webhookRepository->getAllByEventType($webhook_request->event_type);

        foreach ($webhooks as $webhook) {
            if ($post->form_id == $webhook['form_id']) {
                $this->signer = new Signer($webhook['shared_secret']);

                $data = $post->asArray();

                // Attach Webhook Uuid so that service can subsequently identify itself
                // when sending data to the Platform
                $data['webhook_uuid'] = $webhook['webhook_uuid'];

                // If set append the source and destination fields to the request
                // These fields identify the UUIDs of the Post fields which the remot service should
                // treat as the source of data and the destination for any data to be posted back to the Platform
                $data['source_field_key'] = $webhook['source_field_key'] ?: null;
                $data['destination_field_key'] = $webhook['destination_field_key'] ?: null;

                $json = json_encode($data);
                $signature = $this->signer->sign($webhook['url'], $json);

                // This is an asynchronous request, we don't expect a result
                // this can be extended to allow for handling of the returned promise
                //TODO: HANDLE HTTP ERRORS
                $promise = $this->client->request('POST', $webhook['url'], [
                    'headers' => [
                        'X-Ushahidi-Signature' => $signature,
                        'Accept'               => 'application/json'
                    ],
                    'json' => $data
                ]);
            }
        }
    }
}
