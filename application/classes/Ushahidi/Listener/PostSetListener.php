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

use Ushahidi\Core\Entity\NotificationQueueRepository;

class Ushahidi_Listener_PostSetListener extends AbstractListener
{
	protected $repo;
	
	public function setRepo(NotificationQueueRepository $repo)
	{
		$this->repo = $repo;
	}
	
    public function handle(EventInterface $event, $set_id = null, $post_id = null)
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
