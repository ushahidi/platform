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

	public function setChangeLogRepo(ChangeLogRepo $changelog_repo)
	{
		$this->changelog_repo = changelog_repo;
	}

  public function handle(EventInterface $event, $postEntity = null, $event_type = null)
  {

		//there's just one event handler, so here we handle specific event types...
		if($event_type == 'update')
		{
						//send event info off to webhook
						$state = [
							'post_id' => $postEntity->id,
							'event_type' => $event_type
						];

						$entity = $this->repo->getEntity();
						$entity->setState($state);
						$this->repo->create($entity);

				Kohana::$log->add(Log::INFO, 'Can we get just the changes?'.print_r($postEntity->getChanged(), true));

		}else if($event_type == 'create')
		{
						//send event info off to webhook
						$state = [
							'post_id' => $postEntity->id,
							'event_type' => $event_type
						];

						$entity = $this->repo->getEntity();
						$entity->setState($state);
						$this->repo->create($entity);
		}else
		{
				Kohana::$log->add(Log::DEBUG, 'What kind of event just happened? '.$event_type.'!');
		}



  }
}
