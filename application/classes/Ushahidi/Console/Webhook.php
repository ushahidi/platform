<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Webhook Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;

use Ushahidi\Core\Tool\Signer;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\WebhookJobRepository;
use Ushahidi\Core\Entity\WebhookRepository;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Ushahidi_Console_Webhook extends Command
{
	private $db;
	private $postRepository;
	private $webhookRepository;
	private $webhookJobRepository;
	private $signer;
	private $client;

	public function setDatabase(Database $db)
	{
		$this->db = $db;
	}

	public function setWebhookRepo(WebhookRepository $repo)
	{
		$this->webhookRepository = $repo;
	}

	public function setPostRepo(PostRepository $repo)
	{
		$this->postRepository = $repo;
	}

	public function setSigner(Signer $signer)
	{
		$this->signer = $signer;
	}

	public function setWebhookJobRepo(WebhookJobRepository $repo)
	{
		$this->webhookJobRepository = $repo;
	}

	protected function configure()
	{
		$this
			->setName('webhook')
			->setDescription('Manage webhook requests')
			->addArgument('action', InputArgument::OPTIONAL, 'list, send', 'list')
			->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'number of webhook requests to be sent')
			;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		return [
			[
				'Available actions' => 'send'
			]
		];
	}

	protected function executeSend(InputInterface $input, OutputInterface $output)
	{

		$this->client = new GuzzleHttp\Client();

		$limit = $input->getOption('limit');

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

		return [
			[
				'Message' => sprintf('%d webhook requests sent', $count)
			]
		];
	}

	private function generateRequest($webhook_request)
	{
		// Delete queued webhook request
		$this->webhookJobRepository->delete($webhook_request);

		// Get post data
		$post = $this->postRepository->get($webhook_request->post_id);

		// Get webhook data
		$webhook = $this->WebhookRepository->getByEventType($webhook_request->event_type);

		$this->signer = new Signer($webhook->shared_secret);

		$signature  $this->signer->sign($webhook->url, $post->asArray();)

		$headers = ['X-Platform-Signature' => $signature];

		$request = new Request('POST', $webhook->url, $headers, $post);

		$this->client->sendAsync($equest);

		return $request;

	}
}
