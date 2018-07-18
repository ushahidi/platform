<?php

/**
 * Ushahidi Webhook Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Webhook;
use Ushahidi\Core\Entity\WebhookRepository as WebhookRepositoryContract;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\AdminAccess;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

use Log;

class WebhookRepository extends OhanzeeRepository implements WebhookRepositoryContract
{
    use UserContext;
    use AdminAccess;

    protected function getTable()
    {
        return 'webhooks';
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
            'user'
        ] as $fk) {
            if ($search->$fk) {
                $query->where("webhooks.{$fk}_id", '=', $search->$fk);
            }
        }
    }

    public function getEntity(array $data = null)
    {
        return new Webhook($data);
    }

    public function getByEventType($event_type = null)
    {
        return $this->getEntity($this->selectOne(compact('event_type')));
    }

    public function getAllByEventType($event_type = null)
    {
        $query = $this->selectQuery(compact('event_type'));

        $results = $query->execute($this->db);
        return $results->as_array();
    }

    public function getByUUID($webhook_uuid = null)
    {
        return $this->getEntity($this->selectOne(compact('webhook_uuid')));
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $uuid = Uuid::uuid4();
        $uuid = $uuid->toString();

        $state = [
            'user_id' => $entity->user_id,
            'webhook_uuid' => $uuid,
            'created' => time(),
        ];

        return parent::create($entity->setState($state));
    }

    // UpdateRepository
    public function update(Entity $entity)
    {

        $record = $entity->asArray();
        $record['updated'] = time();
        return $this->executeUpdate(['id' => $entity->id], $record);
    }

    public function getSearchFields()
    {
        return [
            'user'
        ];
    }
}
