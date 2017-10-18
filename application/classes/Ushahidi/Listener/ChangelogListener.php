<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Changelog Listener
 *
 * Listens for new posts that are added to a set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Event\AbstractListener;
use League\Event\EventInterface;
use Ushahidi\Core\Entity\PostsChangeLogRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\FormStageRepository;
//use Ushahidi\Core\Entity\PostRepository; // redirection issue! - can't include here
//use Ushahidi\Core\Entity\SetRepository; // redirection issue! - can't include here
use Ushahidi\Core\Traits\RecursiveImplode;

class Ushahidi_Listener_ChangelogListener extends AbstractListener
{
    protected $changelog_repo;
    protected $user_repo;
    protected $set_repo;
    protected $formstages_repo;

    use RecursiveImplode;

    public function setChangeLogRepo(PostsChangeLogRepository $changelog_repo)
    {
        $this->changelog_repo = $changelog_repo;
    }

    public function setUserRepo(UserRepository $user_repo)
    {
        $this->user_repo = $user_repo;
    }

    public function setSetRepo(SetRepository $set_repo)
    {
        $this->set_repo = $set_repo;
    }

    public function setFormStagesRepo(FormStageRepository $formstages_repo)
    {
        $this->formstages_repo = $formstages_repo;
    }

    public function handle(EventInterface $event, $eventEntity = null, $post_id = null, $event_detail = null)
    {
        if(!is_object($eventEntity))
        {
            return;
        }
        Kohana::$log->add(Log::INFO, 'Changelog listener has intercepted some kind of event!: '.print_r($event->getName(), true).'!');
        Kohana::$log->add(Log::INFO, 'Passed entity is: '.print_r(get_class($eventEntity), true).'!');

        if ($event->getName() == 'LoggablePostSetEvent' && get_class($eventEntity) == "Ushahidi\Core\Entity\Set")
        {
            Kohana::$log->add(Log::INFO, 'Handling a LoggablePostSetEvent...');
            try {
                $changelog_state = [
                'post_id'=> $post_id,
                'change_type' => 'Changed collection',
                'item_changed' => 'Collections',
                'content'=> 'Added post to collection: '.print_r($eventEntity->name, true),
                'entry_type'=> 'a',
                ];

                //send this event to the changelog
                $changelog_entity = $this->changelog_repo->getEntity();
                $changelog_entity->setState($changelog_state);
                $this->changelog_repo->create($changelog_entity);
            } catch (Exception $e) {
                Kohana::$log->add(Log::INFO, 'trying to send a post/collection change to changelog.'.print_r($e, true));
            }

        }else if($event->getName() == 'LoggablePostUpdateEvent' && get_class($eventEntity) == "Ushahidi\Core\Entity\Post") {

            $changes_array = $eventEntity->getChanged();
            $flat_changeset = [];
            $flat_changeset = $this->getIsolatedChangesForPost($eventEntity, $changes_array);

            if (count($flat_changeset) > 0) {
                //TODO: RECONSIDER: currently concatenating all the changes into one long string
                // this will be impossible to translate.
                // Maybe do a translation code for each server-side field name, then
                //	let the client group them?
                $human_friendly_log = '- Updated fields.<br/>';
                foreach ($flat_changeset as $new_item => $new_value) {
                    //TODO: are these values already sanitized for HTML display?
                    $human_friendly_log .= '- Changed '.str_replace("_", "-", $new_item).' to "'.$new_value.'"</br>';
                }
                Kohana::$log->add(Log::DEBUG, 'Human-readable log: '.print_r($human_friendly_log, true).'!');
                $changelog_state = [
                        'post_id' => $eventEntity->id,
                        'item_changed' => '',
                        'content' => $human_friendly_log,
                        'entry_type' => 'a',
                    ];
                $changelog_entity = $this->changelog_repo->getEntity();
                $changelog_entity->setState($changelog_state);
                $this->changelog_repo->create($changelog_entity);
            }
        } else {
            Kohana::$log->add(Log::DEBUG, 'Uknown event just happened? '.print_r($event->getName(), true).'!');
        }
    }

    protected function getLabelForCompletedStageId($id)
    {
        $formstage_label = "";
        $formstage_entity = $this->formstages_repo->get($id);
        if(is_object($formstage_entity))
        {
            $formstage_label = $formstage_entity->label;
        }
        return $formstage_label;
    }

    protected function getPostInfoFromPostId($id)
    {
        //lookup post from repo
        return $post_obj;
    }


    // TODO: QUESTION
    //	Rather than guess at the structure of Post, is it enough to log known attributes and keys
    protected function getIsolatedChangesForPost($postEntity, $changed_items)
    {
        $flat_changeset = [];
        $ignored_fields = ['slug'];
        try {
            foreach ($changed_items as $changed_key => $changed_value) {
                if (is_array($changed_value)) {
                    if ($changed_key == 'values') {
                        //go only one level into this array, but then just concat the changes as text, since we're
                        // presumably already at the individual field level
                        foreach ($changed_value as $values_key => $values_val) {
                            $imploded_str = $this->recursiveArrayImplode(" ", $values_val);
                            $flat_changeset = array_merge($flat_changeset, [$values_key => $imploded_str ]);
                        }

                    } elseif ($changed_key == 'completed_stages')
                    {
                        Kohana::$log->add(Log::ERROR, 'Changed stages: '.print_r($changed_items[$changed_key], true));
                        if(is_array($changed_items[$changed_key]))
                        {
                            foreach($changed_items[$changed_key] as $stagekey => $stageval)
                            $stage_label = $this->getLabelForCompletedStageId($stageval);
                            Kohana::$log->add(Log::ERROR, 'Stage completed: '.print_r($stage_label, true));
                            $flat_changeset = array_merge($flat_changeset, ['Task: '.$stage_label  => 'complete']);
                        }

                    }  elseif ($changed_key == 'tags' || $changed_key == 'sets') {
                        Kohana::$log->add(Log::ERROR, 'Tags where changed: '.print_r($changed_items[$changed_key], true));

                        $imploded_str = $this->recursiveArrayImplode(" ", $changed_value);
                        $flat_changeset = array_merge($flat_changeset, [$values_key => $imploded_str ]);
                    }
                } else { // not an array
                        if (!in_array($changed_key, $ignored_fields)) {
                            $flat_changeset = array_merge($flat_changeset, [$changed_key => $changed_value ]);
                        }
                }
            }
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error trying to log a change: '.print_r($e, true));
        }

        Kohana::$log->add(Log::INFO, 'Here is the full changeset: '.print_r($flat_changeset, true));
        return $flat_changeset;
    }

}
