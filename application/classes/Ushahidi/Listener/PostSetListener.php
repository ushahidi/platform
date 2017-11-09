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
use Ushahidi\Core\Entity\SetRepository;

class Ushahidi_Listener_PostSetListener extends AbstractListener
{
    protected $repo;
    protected $changelog_repo;
    //protected $set_repo;

    public function setRepo(NotificationQueueRepository $repo)
    {
        $this->repo = $repo;
    }

    public function setChangeLogRepo(PostsChangeLogRepository $changelog_repo)
    {
        $this->changelog_repo = $changelog_repo;
    }

    /*public function setSetRepo(SetRepository $set_repo)
    {
            //$this->$set_repo = $set_repo;
    }*/

    public function handle(EventInterface $event, $set_obj = null, $post_id = null, $event_type = null)
    {
        //logging a set<->post event in changelog
        Kohana::$log->add(Log::INFO, 'Handling a PostSetEvent...');
        try {
            $contentActivity = 'Unknown action: ';
            if ($event_type == 'add')
            {
                $contentActivity = 'Added post to collection:';
            }else if ($event_type == 'remove'){
                $contentActivity = 'Removed post from collection:';
            }

            $changelog_state = [
                'post_id'=> $post_id,
                'change_type' => 'Changed collection',
                'item_changed' => 'Collections',
                'content'=> $contentActivity.print_r($set_obj->name, true),
                'entry_type'=> 'a',
            ];

            //send this event to the changelog
            $changelog_entity = $this->changelog_repo->getEntity();
            $changelog_entity->setState($changelog_state);
            $this->changelog_repo->create($changelog_entity);

        } catch (Exception $e) {
            Kohana::$log->add(Log::INFO, 'trying to send a post/collection change to changelog.'.print_r($e, true));
        }


        // Insert into Notification Queue
        $state = [
            'set'  => $set_obj->id,
            'post' => $post_id
        ];
        $entity = $this->repo->getEntity();
        $entity->setState($state);
        $this->repo->create($entity);
    }
}
