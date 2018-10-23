<?php

/**
 * Ushahidi Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;

abstract class EloquentRepository implements
    Usecase\CreateRepository,
    Usecase\ReadRepository,
    Usecase\UpdateRepository,
    Usecase\DeleteRepository,
    Usecase\ImportRepository
{
    protected $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the entity for this repository.
     * @param  Array  $data
     * @return Ushahidi\Core\Entity
     */
    abstract public function getEntity(array $data = null);

    /**
     * Converts an array/collection of results into an collection
     * of entities, indexed by the entity id.
     *
     * Included directly instead of using Ushahidi\Core\Traits\CollectionLoader
     * because this implementation returns an Illuminate\Support\Collection
     *
     * @param  Array|Iterable $results
     * @return \Illuminate\Support\Collection
     */
    protected function getCollection($results)
    {
        return Collection::wrap($results)->mapWithKeys(function ($item, $key) {
            $entity = $this->getEntity((array) $item);
            return [$entity->getId() => $entity];
        });
    }

    /**
     * Get the table name for this repository.
     * @return String
     */
    abstract protected function getTable();

    // CreateRepository
    // ReadRepository
    // UpdateRepository
    // DeleteRepository
    public function get($id)
    {
        return $this->getEntity((array) $this->selectOne([
            $this->getTable().'.id' => $id
        ]));
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        return $this->executeInsert($this->removeNullValues($entity->asArray()));
    }

    // UpdateRepository
    public function update(Entity $entity)
    {
        return $this->executeUpdate(['id' => $entity->id], $entity->getChanged());
    }

    // DeleteRepository
    public function delete(Entity $entity)
    {
        return $this->executeDelete(['id' => $entity->id]);
    }

    /**
     * Remove all `null` values, to allow the database to set defaults.
     *
     * @param  Array $data
     * @return Array
     */
    protected function removeNullValues(array $data)
    {
        return array_filter($data, function ($val) {
            return isset($val);
        });
    }

    /**
     * Get a single record meeting some conditions.
     * @param  Array $where hash of conditions
     * @return Array
     */
    protected function selectOne(array $where = [])
    {
        return collect(
            $this->selectQuery($where)->first()
        )->toArray();
    }

    /**
     * Get a count of records meeting some conditions.
     * @param  Array $where hash of conditions
     * @return Integer
     */
    protected function selectCount(array $where = [])
    {
        return $this->connection->table($this->getTable())
            ->select($this->getTable() . '.*') // @todo do we need this?
            ->where($where) // @todo do we need to handle whereIn here too?
            ->count();
    }

    /**
     * Return a SELECT query, optionally with preconditions.
     * @param  Array $where optional hash of conditions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function selectQuery(array $where = [])
    {
        $query = $this->connection->table($this->getTable())
            ->select($this->getTable() . '.*') // @todo do we need this?
            ->where($where) // @todo do we need to handle whereIn here too?
            ;

        return $query;
    }

    /**
     * Create a single record from input and return the created ID.
     * @param  Array $input hash of input
     * @return Integer
     */
    protected function executeInsert(array $input)
    {
        if (!$input) {
            throw new RuntimeException(sprintf(
                'Cannot create an empty record in table "%s"',
                $this->getTable()
            ));
        }

        return $this->connection
            ->table($this->getTable())
            ->insertGetId($input);
    }

    /**
     * Update records from input with conditions and return the number affected.
     * @param  Array $where hash of conditions
     * @param  Array $input hash of input
     * @return Integer
     */
    protected function executeUpdate(array $where, array $input)
    {
        if (!$where) {
            throw new RuntimeException(sprintf(
                'Cannot update every record in table "%s"',
                $this->getTable()
            ));
        }

        // Prevent overwriting created timestamp
        // Probably not needed if `created` is set immutable in Entity
        if (array_key_exists('created', $input)) {
            unset($input['created']);
        }

        if (!$input) {
            return 0; // nothing would be updated, just ignore
        }

        return $this->connection
            ->table($this->getTable())
            ->where($where)
            ->update($input);
    }

    /**
     * Delete records with conditions and return the number affected.
     * @param  Array $where hash of conditions
     * @return Integer
     */
    protected function executeDelete(array $where)
    {
        if (!$where) {
            throw new RuntimeException(sprintf(
                'Cannot delete every record in table "%s"',
                $this->getTable()
            ));
        }

        return $this->connection
            ->table($this->getTable())
            ->where($where)
            ->delete();
    }

    /**
     * Check if an entity with the given id exists
     * @param  int $id
     * @return bool
     */
    public function exists($id)
    {
        return $this->connection->table($this->getTable())
            ->select($this->getTable() . '.*') // @todo do we need this?
            ->where($where) // @todo do we need to handle whereIn here too?
            ->exists();
    }
}
