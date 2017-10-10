<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tos Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostsChangeLog;
use Ushahidi\Core\Entity\PostsChangeLogRepository;
//use Ushahidi\Core\SearchData;
//use Ushahidi\Core\ReadData;
use Ushahidi\Core\Traits\UserContext;

class Ushahidi_Repository_Post_ChangeLog extends Ushahidi_Repository implements PostsChangeLogRepository
{
    use UserContext;

    // Ushahidi_Repository
    protected function getTable()
    {
        return 'posts_changelog';
    }

    public function getSearchFields()
  	{
      return ['post_id', 'entry_id'];
    }

    public function getEntity(Array $data = null)
    {
        return new PostsChangeLog($data);
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $user = $this->getUser();
        $data = $entity->asArray();

        $data['created']  = time();
        $data['user_id'] = $this->getUserId();
        //$data['entry_type']  = 'm'; // m for manual
        Kohana::$log->add(Log::INFO, print_r('Adding a new record to changelog', true));

        return $this->executeInsert($this->removeNullValues($data));
    }


    //TODO: this is probably not the way to do this, plus we should
    // eventually handle limits, offsets somehow

    // See: Tags
    public function getPostChangelogs($entity_id)
    {
       return DB::select('posts_changelog.id', 'post_id', 'user_id', 'realname',
          'entry_type', 'posts_changelog.created', 'change_type', 'item_changed',
           'content')
          ->from('posts_changelog')
          ->where('post_id', '=', $entity_id)

          ->join('users')->on('posts_changelog.user_id', '=', 'users.id')
          ->order_by('posts_changelog.created','desc')

          ->execute($this->db)
          ->as_array();
    	}

}
