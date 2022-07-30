<?php

/**
 * Ushahidi Notification Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Repository;

use Ushahidi\Core\Tools\SearchData;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Entity\Notification;
use Ushahidi\Contracts\Repository\Entity\NotificationRepository as NotificationRepositoryContract;

class NotificationRepository extends OhanzeeRepository implements NotificationRepositoryContract
{
    use UserContext;
    use AdminAccess;

    protected function getId(Entity $entity)
    {
        $result = $this->selectQuery()
            ->where('user_id', '=', $entity->user_id)
            ->and_where('set_id', '=', $entity->set_id)
            ->execute($this->db());
        return $result->get('id', 0);
    }

    protected function getTable()
    {
        return 'notifications';
    }

    // OhanzeeRepository
    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;

        $user = $this->getUser();

        // Limit search to user's records unless they are admin
        // or if we get user=me as a search param
        if (! $this->isUserAdmin($user) || $search->user === 'me') {
            $search->user = $this->getUserId();
        }

        foreach ([
            'user',
            'set',
        ] as $fk) {
            if ($search->$fk) {
                $query->where("notifications.{$fk}_id", '=', $search->$fk);
            }
        }
    }

    public function getEntity(array $data = null)
    {
        return new Notification($data);
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $id = $this->getId($entity);

        if ($id) {
            // No need to insert a new record.
            // Instead return the id of the notification that exists
            return $id;
        }

        $state = [
            'user_id' => $entity->user_id,
            'created' => time(),
        ];

        return parent::create($entity->setState($state));
    }

    public function getSearchFields()
    {
        return [
            'user',
            'set'
        ];
    }
}
