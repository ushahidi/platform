<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tos Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostsChangeLog;
use Ushahidi\Core\Entity\PostsChangeLogRepository;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Tool\Permissions\AclTrait;

class Ushahidi_Repository_Post_ChangeLog extends Ushahidi_Repository
implements PostsChangeLogRepository
{
    use UserContext;

    // Ushahidi_Repository
    protected function getTable()
    {
        return 'posts_changelog';
    }

    public function getEntity(Array $data = null)
    {
        return new PostsChangeLog($data);
    }

    public function getSearchFields()
  	{
      return ['post_id', 'id'];
    }


    // CreateRepository
    public function create(Entity $entity)
    {
        $user = $this->getUser();
        $data = $entity->asArray();

        $data['created']  = time();
        $data['user_id'] = $this->getUserId();
        //$data['entry_type']  = 'm'; // m for manual

        return $this->executeInsert($this->removeNullValues($data));
    }


	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->post_id) {
			$query->where('post_id', '=', $search->post_id);
		}

	}

  // Override SearchRepository
	public function setSearchParams(SearchData $search)
	{

		$post_id = null;
		if ($search->post_id) {
			$post_id = $search->post_id;
		}

		$this->search_query = $this->selectQuery([], $post_id);

		$sorting = $search->getSorting();

		if (!empty($sorting['orderby'])) {
			$this->search_query->order_by(
				$this->getTable() . '.' . $sorting['orderby'],
				Arr::get($sorting, 'order')
			);
		}

		if (!empty($sorting['offset'])) {
			$this->search_query->offset($sorting['offset']);
		}

		if (!empty($sorting['limit'])) {
			$this->search_query->limit($sorting['limit']);
		}

		// apply the unique conditions of the search
		$this->setSearchConditions($search);
	}


}
