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

use Ushahidi\Core\Entity\WebhookQueueRepository;

class Ushahidi_Listener_PostListener extends AbstractListener
{
	protected $repo;

	public function setRepo(WebhookQueueRepository $repo)
	{
		$this->repo = $repo;
	}

  public function handle(EventInterface $event, $post_id, $webhook_id)
  {
		// Insert into Notification Queue
		$state = [
			'post' => $post_id,
			'webhook' => $webhook_id
		];

		$entity = $this->repo->getEntity();
		$entity->setState($state);
		$this->repo->create($entity);
  }
}
