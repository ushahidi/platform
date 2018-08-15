<?php

/**
 * Ushahidi Post Comment Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Post;

use Ohanzee\DB;
use Ohanzee\Database;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostComment;
use Ushahidi\Core\Entity\PostCommentRepository;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\App\Repository\OhanzeeRepository;

use League\Event\ListenerInterface;

class CommentRepository extends OhanzeeRepository implements PostCommentRepository
{
    // Provides getUser()
    use UserContext;


    // OhanzeeRepository
    protected function getTable()
    {
        return 'comments';
    }

    // OhanzeeRepository
    public function getSearchFields()
    {
        return [
            'post_id',
            'user_id'
        ];
    }

    // OhanzeeRepository
    public function getAllForPost($post_id)
    {
        $query = parent::selectQuery(compact('post_id'));

        return $this->getCollection($results->as_array());
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        return new Comment($data);
    }

}
