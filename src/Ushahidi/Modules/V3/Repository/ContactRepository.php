<?php

/**
 * Ushahidi Contact Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository;

use Ohanzee\DB;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Entity\Contact;
use Illuminate\Support\Collection;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Contracts\Repository\CreateRepository;
use Ushahidi\Contracts\Repository\SearchRepository;
use Ushahidi\Contracts\Repository\UpdateRepository;
use Ushahidi\Contracts\Repository\Entity\ContactRepository as ContactRepositoryContract;

class ContactRepository extends OhanzeeRepository implements
    ContactRepositoryContract,
    CreateRepository,
    UpdateRepository,
    SearchRepository
{
    use UserContext;
    use AdminAccess;
    // Use Event trait to trigger events
    // use \Ushahidi\Core\Concerns\Event;

    use Concerns\UsesBulkAutoIncrement;

    protected function getId(Entity $entity)
    {
        $result = $this->selectQuery()
            ->where('user_id', '=', $entity->user_id)
            ->and_where('contact', '=', $entity->contact)
            ->execute($this->db());
        return $result->get('id', 0);
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'contacts';
    }

    // CreateRepository
    // ReadRepository
    public function getEntity(array $data = null)
    {
        return new Contact($data);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return [
            'contact', 'type', 'user', 'data_source'
        ];
    }

    // OhanzeeRepository
    protected function setSearchConditions(SearchData $search)
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
        ] as $fk) {
            if ($search->$fk) {
                $query->where("contacts.{$fk}_id", '=', $search->$fk);
            }
        }

        foreach ([
            'type',
            'data_source',
            'contact'
        ] as $key) {
            if ($search->$key) {
                $query->where("contacts.{$key}", '=', $search->$key);
            }
        }
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $id = $this->getId($entity);

        // @todo perhaps allow fields for existing entity to be defined when an entity is being created
        if ($id) {
            // No need to insert a new record.
            // Instead return the id of the contact that exists
            return $id;
        }

        $state = [
            'created'  => time(),
        ];

        return parent::create($entity->setState($state));
    }

    public function createMany(Collection $collection) : array
    {
        $this->checkAutoIncMode();

        $first = $collection->first()->asArray();
        unset($first['country_code']);
        $columns = array_keys($first);

        $values = $collection->map(function ($entity) {
            $data = $entity->asArray();
            unset($data['country_code']);

            $data['created'] = time();

            return $data;
        })->all();

        $query = DB::insert($this->getTable())
            ->columns($columns);

        call_user_func_array([$query, 'values'], $values);

        list($insertId, $created) = $query->execute($this->db());

        return range($insertId, $insertId + $created - 1);
    }

    // UpdateRepository
    public function update(Entity $entity)
    {
        $state = [
            'updated'  => time(),
        ];

        return parent::update($entity->setState($state));
    }

    // ContactRepository
    public function getByContact($contact, $type)
    {
        return $this->getEntity($this->selectOne(compact('contact', 'type')));
    }

    // ContactRepository
    public function getNotificationContacts($set_id, $limit = false, $offset = 0)
    {
        $query = DB::select('contacts.id', 'contacts.type', 'contacts.contact')
            ->distinct(true)
            ->from('contacts')
            ->join('notifications')
            ->on('contacts.user_id', '=', 'notifications.user_id')
            ->where('contacts.can_notify', '=', '1');

        if ($set_id) {
            $query->and_where('set_id', '=', $set_id);
        }

        if ($limit) {
            $query->limit($limit);
        }

        if ($offset) {
            $query->offset($offset);
        }

        $results =  $query->execute($this->db());

        return $this->getCollection($results->as_array());
    }
}
