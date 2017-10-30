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
		$this->db = service('kohana.db');
		$this->webhookRepository = service('repository.webhook');
		$this->postRepository = service('repository.post');
		$this->webhookJobRepository = service('repository.webhook.job');
	}

	public function handle()
	{
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
		//$this->webhookJobRepository->delete($webhook_request);

		// Get post data
		$post = $this->postRepository->get($webhook_request->post_id);
		$json = json_encode($post->asArray());

		// Get webhook data
		$webhook = $this->webhookRepository->getByEventType($webhook_request->event_type);

		$this->signer = new Signer($webhook->shared_secret);

		$signature = $this->signer->sign($webhook->url, $json);

		// This is an asynchronous request, we don't expect a result
		// this can be extended to allow for handling of the returned promise
		$promise = $this->client->request('POST', $webhook->url, [
			'headers' => [
				'X-Platform-Signature' => $signature,
				'Accept'               => 'application/json'
			],
			'json' => $post->asArray()
		]);
	}
}
