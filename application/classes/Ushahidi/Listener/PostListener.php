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
use Ushahidi\Core\Entity\PostsChangeLogRepository;

class Ushahidi_Listener_PostListener extends AbstractListener
{
	protected $repo;
	protected $webhook_repo;
	protected $changelog_repo;

	public function setRepo(WebhookJobRepository $repo)
	{
		$this->repo = $repo;
	}

	public function setWebhookRepo(WebhookRepository $webhook_repo)
	{
		$this->webhook_repo = $webhook_repo;
	}

	public function setChangeLogRepo(PostsChangeLogRepository $changelog_repo)
	{
			$this->changelog_repo = $changelog_repo;
	}

//TODO: this is ugly, because we're only receiving the ID here.
//	 is there a way to handle multiple events with different methods/signatures?
  public function handle(EventInterface $event, $postEntity = null, $event_type = null)
  {
		//Kohana::$log->add(Log::INFO, 'This kind of event was passed: '.print_r($event_type, true) );
		//there's just one event handler, so for now, let's handle specific event types in here...
		if($event_type == 'update')
		{
						//TODO: check on fields within fields -- tasks, tags, etc
						//spec reqs: Intercept the following:
						//		1. Changed fields
						//		2. Changed status
						//		3. Changed tags
						//		4. Added to/removed from collections -- TODO: this might have to be caught in a collection
						//		5. Tasks updated?

						try {
						$changed_fields = $postEntity->getChanged();

						//NOTE...we need to create a log entry for EVERY SINGLE CHANGE....
						foreach ($changed_fields as $changed_field => $new_val)
						{
							Kohana::$log->add(Log::INFO, 'Field changed: '.print_r($changed_field, true) );

							//TODO: intercept tasks, values, or completed_stages and break them into component pieces with labels

							if (is_array($new_val) || $changed_field == 'tags' ||
														$changed_field == 'completed_stages' || $changed_field == 'values')
							{
								Kohana::$log->add(Log::INFO, $changed_field.' val is an array.'.print_r($new_val, true) );
								$new_val = implode(flatten_array($new_val));
									//continue;
							}
							Kohana::$log->add(Log::INFO, 'Changed field ['.$changed_field."] to new value [".print_r($new_val, true)."]" );

								//TOASK: should we skip these fields?? we know they
								if ($changed_field != 'post_date' && $changed_field != 'updated')
								{
									//TOASK: should this data mapping happen HERE, or in/via the Entity?
										$changelog_state = [
											'post_id' => $postEntity->id,
											'item_changed' => $changed_field,
											'new_status' => $new_val,
											'entry_type' => 'a',
										];
										$changelog_entity = $this->changelog_repo->getEntity();
										$changelog_entity->setState($changelog_state);
										$this->changelog_repo->create($changelog_entity);
								}
						}
					}catch (Exception $e)
					{
							Kohana::$log->add(Log::ERROR, 'Error happened on CHANGE! '.print_r($e, true) );
					}

		}else if($event_type == 'create')
		{
						Kohana::$log->add(Log::INFO, 'Entity grabbed? '.print_r($postEntity, true) );

						//send event info off to webhook
						$state = [
							'post_id' => $postEntity,  // note, this is not really an entity, just an id
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

//TODO: replace this with a common function somwhere?
function flatten_array(array $array) {
    $flattened_array = array();
    array_walk_recursive($array, function($a, $b) use (&$flattened_array) { $flattened_array[] = $a.' '.$b; });
    return $flattened_array;
}
