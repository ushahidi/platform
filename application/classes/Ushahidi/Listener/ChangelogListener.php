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
//use Ushahidi\Core\Entity\PostRepository; // redirection issue! - can't include here
//use Ushahidi\Core\Entity\SetRepository; // redirection issue! - can't include here
use Ushahidi\Core\Traits\RecursiveImplode;

class Ushahidi_Listener_ChangelogListener extends AbstractListener
{
    protected $changelog_repo;
    protected $user_repo;
    protected $set_repo;

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

    public function handle(EventInterface $event, $postEntity = null, $event_type = null)
    {
        Kohana::$log->add(Log::INFO, 'Changelog listener has intercepted an event! '.print_r($event_type, true).'!');
        Kohana::$log->add(Log::INFO, 'What object is this?: '.print_r($postEntity, true));

        if ($event_type == 'update') {
            $changes_array = $postEntity->getChanged();
            $flat_changeset = [];
            $flat_changeset = $this->getIsolatedChangesForPost($postEntity, $changes_array);

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
        } elseif ($event_type == 'create') {

            Kohana::$log->add(Log::DEBUG, 'Post creation event just happened '.$event_type.'!');

        } else {
            Kohana::$log->add(Log::DEBUG, 'What kind of event just happened? '.$event_type.'!');
        }
    }

    protected function getUserInfoFromUserId($id)
    {
        //lookup user from repo
        return $user_obj;
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
                    } elseif ($changed_key == 'tags' || $changed_key == 'sets' || $changed_key == 'completed_stages') {
                        $imploded_str = $this->recursiveArrayImplode(" ", $values_val);
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

        Kohana::$log->add(Log::ERROR, 'Here is the full changeset: '.print_r($flat_changeset, true));
        return $flat_changeset;
    }

}
