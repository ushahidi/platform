<?php

/**
 * Ushahidi Use Case for API Collections
 *
 * Exposes a simple SCRUD interface for basic API operations.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\API;

use Ushahidi\Entity;

class Collection
{
	protected $repo;
	// protected $auth;

	private $allowed_orders = array('asc', 'desc');

	private $search = [];
	private $order = [];
	private $limit;
	private $offset;

	public function __construct(CollectionRepository $repo)
	{
		$this->repo = $repo;
	}

	/**
	 * Set search query terms.
	 * @param  String  $field
	 * @param  String  $search
	 * @return $this
	 */
	public function query($field, $search)
	{
		// todo: validate search fields using repo?
		$this->search[$field] = $search;
		return $this;
	}

	/**
	 * Set search sorting.
	 * @param  String  $field
	 * @param  String  $order
	 * @return $this
	 */
	public function orderBy($field, $order = null)
	{
		if ($order) {
			$order = strtolower($order);
			if (!in_array($order, $this->allowed_orders)) {
				throw new \InvalidArgumentException(
					'Invalid order direction: "' . $order . '", ' .
					'valid directions are: "' . implode(', ', $this->allowed_orders));
			}
		}

		// todo: validate field names through repo?
		$this->order[$field] = $order;
		return $this;
	}

	/**
	 * Set search limit.
	 * @param  Integer  $limit
	 * @return $this
	 */
	public function limit($limit)
	{
		$this->limit = intval($limit) ?: null;
		return $this;
	}

	/**
	 * Set search offset.
	 * @param  Integer  $offset
	 * @return $this
	 */
	public function offset($offset)
	{
		$this->offset = intval($offset) ?: null;
		return $this;
	}

	/**
	 * Search for entities in storage.
	 * @param  \Ushahidi\Entity $entity
	 * @return Array [\Ushahidi\Entity, ...]
	 */
	public function search(Entity $entity)
	{
		// todo: $this->auth->isAllowed('search', $entity);
		return $this->repo->search($entity, $this->search, $this->order, $this->limit, $this->offset);
	}

	/**
	 * Create a new entity in storage.
	 * @param  \Ushahidi\Entity $entity
	 * @return Boolean
	 */
	public function create(Entity $entity)
	{
		// todo: $this->auth->isAllowed('create', $entity);
		return $this->repo->create($entity);
	}

	/**
	 * Read entities in storage.
	 * @param  \Ushahidi\Entity $entity
	 * @return Array [\Ushahidi\Entity, ...]
	 */
	public function read(Entity $entity)
	{
		// todo: $this->auth->isAllowed('read', $entity);
		return $this->repo->read($entity);
	}

	/**
	 * Update an entity in storage.
	 * @param  \Ushahidi\Entity $entity
	 * @return Boolean
	 */
	public function update(Entity $entity)
	{
		// todo: $this->auth->isAllowed('update', $entity);
		return $this->repo->update($entity);
	}

	/**
	 * Update an entity in storage.
	 * @param  \Ushahidi\Entity $entity
	 * @return Boolean
	 */
	public function delete(Entity $entity)
	{
		// todo: $this->auth->isAllowed('delete', $entity);
		return $this->repo->delete($entity);
	}
}
