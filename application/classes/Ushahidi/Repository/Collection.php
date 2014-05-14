<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Collection CRUD Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity;
use Ushahidi\Tool\Authenticator;
use Ushahidi\Usecase\API\CollectionRepository;

abstract class Ushahidi_Repository_Collection implements CollectionRepository
{
	protected $auth;

	public function __construct(Authenticator $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * @return string
	 */
	abstract protected function getTable();

	/**
	 * @param  Array  $data  initial properties
	 * @return \Ushahidi\Entity
	 */
	abstract protected function getEntity(Array $data = NULL);

	// CollectionRepository
	public function search(Entity $entity, Array $search, Array $order = NULL, $limit = NULL, $offset = NULL)
	{
		$query = DB::select('*')->from($this->getTable());

		$data = array_filter($entity->asArray());
		if ($data) {
			foreach ($data as $field => $value) {
				$query->where($field, '=', $value);
			}
		}

		if ($search) {
			foreach ($search as $field => $value) {
				$query->where($field, 'LIKE', "%$value%");
			}
		}

		if ($order) {
			foreach ($order as $field => $direction) {
				$query->order_by($field, $direction);
			}
		}

		if ($limit) {
			$query->limit($limit);
		}

		if ($offset) {
			$query->offset($offset);
		}

		$results = $query->execute();
		return $this->convertArray($results->as_array());
	}

	private function convertArray(Array $results)
	{
		$entities = [];
		foreach ($results as $row)
		{
			$entity = $this->getEntity($row);
			if ($this->auth->isAllowed($entity, 'get'))
			{
				$entities[] = $entity;
			}
		}
		return $entities;
	}

	// CollectionRepository
	public function create(Entity $entity)
	{
		$data = array_filter($entity->asArray());
		unset($data['id']); // always autoinc

		$query = DB::insert($this->getTable())
			->columns(array_keys($data))
			->values(array_values($data))
			;

		list($entity->id, $count) = $query->execute();
		return (bool) $count;
	}

	// CollectionRepository
	public function read(Entity $entity)
	{
		$query = DB::select('*')
			->from($this->getTable())
			;

		$data = array_filter($entity->asArray());
		foreach ($data as $field => $value)
		{
			$query->where($field, '=', $value);
		}

		$results = $query->execute();
		return $this->convertArray($results->as_array());
	}

	// CollectionRepository
	public function update(Entity $entity)
	{
		if (!$entity->id)
			throw new InvalidArgumentException("Cannot update an entity without an id");

		$data = array_filter($entity->asArray());
		unset($data['id']); // never update id

		$query = DB::update($this->getTable())
			->set($data)
			->where('id', '=', $entity->id)
			;

		$count = $query->execute();
		return TRUE;
	}

	// CollectionRepository
	public function delete(Entity $entity)
	{
		if (!$entity->id)
			throw new InvalidArgumentException("Cannot remove an entity without an id");

		$query = DB::delete($this->getTable())
			->where('id', '=', $entity->id)
			;

		$count = $query->execute();
		return (bool) $count;
	}
}
