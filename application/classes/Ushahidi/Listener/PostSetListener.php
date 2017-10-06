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
use Ushahidi\Core\Entity\PostsChangeLogRepository;

class Ushahidi_Listener_PostSetListener extends AbstractListener
{
	protected $repo;
	protected $changelog_repo;

	public function setRepo(NotificationQueueRepository $repo)
	{
		$this->repo = $repo;
	}


	public function setChangeLogRepo(PostsChangeLogRepository $changelog_repo)
	{
			try
			{
				$this->changelog_repo = $changelog_repo;
			}catch(Exception $e)
			{
				Kohana::$log->add(Log::ERROR, print_r($e, true) );
			}
	}


  public function handle(EventInterface $event, $set_id = null, $post_id = null, $event_type = null)
  {
		Kohana::$log->add(Log::INFO, 'This post: '.$post_id.' was '.$event_type.' this set: '.print_r($set_id, true) );

	try {
		$changelog_state = [
				'post_id'=> $post_id,
				'change_type' => 'Changed collection',
				'item_changed' => 'Collections',
				'content'=> $set_id,
				'entry_type'=> 'a',
		];

		//handle changes to collections
		$changelog_entity = $this->changelog_repo->getEntity();
		$changelog_entity->setState($changelog_state);
		$this->changelog_repo->create($changelog_entity);
	}catch (Exception $e)
	{
		Kohana::$log->add(Log::INFO, 'trying to handle a collection change event.' );
	}

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
