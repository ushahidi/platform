<?php defined('SYSPATH') or die('No direct script access');

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

use League\Event\AbstractListener;
use League\Event\EventInterface;

use Ushahidi\Core\Entity\WebhookJobRepository;
use Ushahidi\Core\Entity\WebhookRepository;

class Ushahidi_Listener_PostListener extends AbstractListener
{
	protected $repo;

	protected $webhook_repo;

	public function setRepo(WebhookJobRepository $repo)
	{
		$this->repo = $repo;
	}

	public function setWebhookRepo(WebhookRepository $webhook_repo)
	{
		$this->webhook_repo = $webhook_repo;
	}

  public function handle(EventInterface $event, $post_id = null, $event_type = null)
  {
		$state = [
			'post_id' => $post_id,
			'event_type' => $event_type
		];

		$entity = $this->repo->getEntity();
		$entity->setState($state);
		$this->repo->create($entity);
  }
}
