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

		if($event_type == 'update')
		{
				$changed_fields_q = $postEntity->getChanged();
				$flat_changeset = [];

				//fields to ignore
				$ignore_fields = ['post_date', 'updated'];

				$this->traverseChangedArray($postEntity, $changed_fields_q, $flat_changeset);
				Kohana::$log->add(Log::DEBUG, 'Here is the flat_changeset: '.print_r($flat_changeset, true) );

				//TODO:RECONSIDER: currently concatenating all the changes into one long string
				if (count($flat_changeset) > 0)
				{
					$human_friendly_log = '- Updated fields.<br/>';
					foreach($flat_changeset as $new_item => $new_value)
					{
						$human_friendly_log .= '- Changed '.$new_item.' to "'.$new_value.'"</br>';
					}
					$changelog_state = [
						'post_id' => $postEntity->id,
						'item_changed' => '',
						'content' => $human_friendly_log,
						'entry_type' => 'a',
					];
					$changelog_entity = $this->changelog_repo->getEntity();
					$changelog_entity->setState($changelog_state);
					$this->changelog_repo->create($changelog_entity);
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

	protected function traverseChangedArray($postEntity, $changed_items, &$flat_changeset)
	{
			foreach($changed_items as $changed_key => $changed_value)
			{
				if (is_array($changed_value))
				{
					foreach($postEntity->getAllChangedFor($changed_key) as $newkey => $newval)
					{
						if (is_array($changed_value[$newval]))
						{
							//just implode this, because we're already down to the individual field
							$newcontent = recursiveImplode(" ", $changed_value[$newval]);
							$addme = [$newval => $newcontent ];
							$flat_changeset = array_merge($flat_changeset, $addme);
						}else {
							$addme = [$newval => $changed_value[$newval] ];
							$flat_changeset = array_merge($flat_changeset, $addme);
						}
					}
					//TODO: NOTE we can't recurse here, because this isn't the same data structure, so getting changed key won't work
					//traverseChangedArray($postEntity, $postEntity->getAllChangedFor($changed_key), $flat_changeset);
				}else { // not array
					$addme = [$changed_key => $changed_value ];
					$flat_changeset = array_merge($flat_changeset, $addme);
				}
			}
	}

}





function recursiveImplode($sep, $givenArray)
{
	Kohana::$log->add(Log::INFO, 'Called recursiveImplode with '.print_r($givenArray, true) );
	$concat_str = "";
	foreach($givenArray as $item)
	{
		if (is_array($item))
		{
				return recursiveImplode($sep, $item).$sep;
		}else{
				$concat_str .= $item.$sep;
		}
	}
	return $concat_str;
}
