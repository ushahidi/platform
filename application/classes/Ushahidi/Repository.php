<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

abstract class Ushahidi_Repository
{
	protected $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	/**
	 * Get the table name for this repository.
	 * @return String
	 */
	abstract protected function getTable();

	/**
	 * Get the entity for this repository.
	 * @param  Array  $data
	 * @return Ushahidi\Entity
	 */
	abstract protected function getEntity(Array $data = null);

	/**
	 * Cleans input, removing empty values, and dropping unwanted keys.
	 * @param  Array $input     hash of input
	 * @param  Array $drop_keys list of keys to drop
	 * @return Array
	 */
	protected function cleanInput(Array $input, Array $drop_keys = Null)
	{
		if ($drop)
		{
			$input = array_diff_key($input, array_flip($drop_keys));
		}
		return array_filter($input);
	}

	/**
	 * Get a single record meeting some conditions.
	 * @param  Array $where hash of conditions
	 * @return Array
	 */
	protected function selectOne(Array $where = [])
	{
		$result = $this->selectQuery($where)
			->limit(1)
			->execute($this->db);
		return $result->current();
	}

	/**
	 * Get a count of records meeting some conditions.
	 * @param  Array $where hash of conditions
	 * @return Integer
	 */
	final protected function selectCount(Array $where = [])
	{
		$result = $this->selectQuery($where)
			->select([DB::expr('COUNT(*)'), 'total'])
			->execute($this->db);
		return $result->get('total') ?: 0;
	}

	/**
	 * Return a SELECT query, optionally with preconditions.
	 * @param  Array $where optional hash of conditions
	 * @return Database_Query_Builder_Select
	 */
	protected function selectQuery(Array $where = [])
	{
		$query = DB::select()->from($this->getTable());
		foreach ($where as $column => $value)
		{
			$predicate = is_array($value) ? 'IN' : '=';
			$query->where($column, $predicate, $value);
		}
		return $query;
	}

	/**
	 * Create a single record from input and return the created ID.
	 * @param  Array $input hash of input
	 * @return Integer
	 */
	final protected function insert(Array $input)
	{
		$query = DB::insert($this->getTable())
			->columns(array_keys($input))
			->values(array_values($input))
			;

		list($id) = $query->execute($this->db);
		return $id;
	}

	/**
	 * Update records from input with conditions and return the number affected.
	 * @param  Array $where hash of conditions
	 * @param  Array $input hash of input
	 * @return Integer
	 */
	final protected function update(Array $where, Array $input)
	{
		if (!$where)
			throw new RuntimeException(sprintf('Cannot update every record in table "%s"', $table));

		$query = DB::update($this->getTable())->set($input);
		foreach ($where as $column => $value)
		{
			$query->where($column, '=', $value);
		}

		$count = $query->execute($this->db);
		return $count;
	}

	/**
	 * Delete records with conditions and return the number affected.
	 * @param  Array $where hash of conditions
	 * @return Integer
	 */
	final protected function delete(Array $where)
	{
		if (!$where)
			throw new RuntimeException(sprintf('Cannot to delete every record in table "%s"', $table));

		$query = DB::delete($this->getTable());
		foreach ($where as $column => $value)
		{
			$query->where($column, '=', $value);
		}

		$count = $query->execute($this->db);
		return $count;
	}

	protected function getCollection(Array $results)
	{
		$collection = [];
		foreach ($results as $row) {
			$entity = $this->getEntity($row);
			$collection[$entity->id] = $entity;
		}
		return $collection;
	}
}

