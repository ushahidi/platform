<?php

/**
 * Ushahidi Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Repositories;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Repository;
use Illuminate\Support\Collection;
use Illuminate\Database\ConnectionResolverInterface;

abstract class EloquentRepository implements
    Repository\CreateRepository,
    Repository\ReadRepository,
    Repository\UpdateRepository,
    Repository\DeleteRepository,
    Repository\ImportRepository
{
    protected $resolver;

    public function __construct(ConnectionResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get current connection
     *
     * @return \Illuminate\Database\Connection;
     */
    protected function connection()
    {
        return $this->resolver->connection();
    }

    /**
     * Get the entity for this repository.
     *
     * @param  array  $data
     *
     * @return Entity
     */
    abstract public function getEntity(array $data = null);

    /**
     * Converts an array/collection of results into an collection
     * of entities, indexed by the entity id.
     *
     * Included directly instead of using \Ushahidi\Core\Concerns\CollectionLoader
     * because this implementation returns an \Illuminate\Support\Collection
     *
     * @param array|\Iterable $results
     *
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
     *
     * @return string
     */
    abstract protected function getTable();

    // CreateRepository
    // ReadRepository
    // UpdateRepository
    // DeleteRepository
    public function get($id)
    {
        return $this->getEntity((array) $this->selectOne([
            $this->getTable() . '.id' => $id
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
     * @param  array $data
     *
     * @return array
     */
    protected function removeNullValues(array $data)
    {
        return array_filter($data, function ($val) {
            return isset($val);
        });
    }

    /**
     * Get a single record meeting some conditions.
     *
     * @param  array $where hash of conditions
     *
     * @return array
     */
    protected function selectOne(array $where = [])
    {
        return collect(
            $this->selectQuery($where)->first()
        )->toArray();
    }

    /**
     * Get a count of records meeting some conditions.
     *
     * @param  array $where hash of conditions
     *
     * @return integer
     */
    protected function selectCount(array $where = [])
    {
        return $this->connection()->table($this->getTable())
            ->select($this->getTable() . '.*') // @todo do we need this?
            ->where($where) // @todo do we need to handle whereIn here too?
            ->count();
    }

    /**
     * Return a SELECT query, optionally with preconditions.
     *
     * @param  array $where optional hash of conditions
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function selectQuery(array $where = [])
    {
        $query = $this->connection()->table($this->getTable())
            ->select($this->getTable() . '.*') // @todo do we need this?
            ->where($where) // @todo do we need to handle whereIn here too?
        ;

        return $query;
    }

    /**
     * Create a single record from input and return the created ID.
     *
     * @param  array $input hash of input
     *
     * @return integer
     */
    protected function executeInsert(array $input)
    {
        if (!$input) {
            throw new \RuntimeException(sprintf(
                'Cannot create an empty record in table "%s"',
                $this->getTable()
            ));
        }

        return $this->connection()
            ->table($this->getTable())
            ->insertGetId($input);
    }

    /**
     * Update records from input with conditions and return the number affected.
     *
     * @param  array $where hash of conditions
     *
     * @param  array $input hash of input
     *
     * @return integer
     */
    protected function executeUpdate(array $where, array $input)
    {
        if (!$where) {
            throw new \RuntimeException(sprintf(
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

        return $this->connection()
            ->table($this->getTable())
            ->where($where)
            ->update($input);
    }

    /**
     * Delete records with conditions and return the number affected.
     *
     * @param  array $where hash of conditions
     *
     * @return integer
     */
    protected function executeDelete(array $where)
    {
        if (!$where) {
            throw new \RuntimeException(sprintf(
                'Cannot delete every record in table "%s"',
                $this->getTable()
            ));
        }

        return $this->connection()
            ->table($this->getTable())
            ->where($where)
            ->delete();
    }

    /**
     * Check if an entity with the given id exists
     *
     * @param  int $id
     *
     * @return bool
     */
    public function exists($id)
    {
        return $this->connection()->table($this->getTable())
            ->select($this->getTable() . '.*') // @todo do we need this?
            ->where($where) // @todo do we need to handle whereIn here too?
            ->exists();
    }
}
