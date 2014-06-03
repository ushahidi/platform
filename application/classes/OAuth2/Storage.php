<?php defined('SYSPATH') or die('No direct script access');

/**
 * OAuth2 Storage CRUD
 *
 * License is MIT, to be more compatible with PHP League.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\OAuth2
 * @copyright  2014 Ushahidi
 * @license    http://mit-license.org/
 * @link       http://github.com/php-loep/oauth2-server
 */

abstract class OAuth2_Storage {

	protected $db = 'default';

	public function __construct($db = null)
	{
		if ($db)
		{
			$this->db = $db;
		}
	}

	private function apply_where_to_query(Database_Query $query, array $where)
	{
		foreach ($where as $col => $value)
		{
			$query->where($col, is_array($value) ? 'IN' : '=', $value);
		}
		return $query;
	}

	protected function select_results(Database_Query $query)
	{
		$results = $query->execute($this->db);
		return count($results) ? $results->as_array() : FALSE;
	}

	protected function select_one_result(Database_Query $query)
	{
		$results = $query->execute($this->db);
		return count($results) ? $results->current() : FALSE;
	}

	protected function select_one_column(Database_Query $query, $column)
	{
		$results = $query->execute($this->db);
		return count($results) ? $results->get($column) : FALSE;
	}

	protected function select($table, array $where = NULL)
	{
		$query = DB::select()
			->from($table);
		if ($where)
		{
			$this->apply_where_to_query($query, $where);
		}
		return $query;
	}

	protected function insert($table, array $data)
	{
		$query = DB::insert($table)
			->columns(array_keys($data))
			->values(array_values($data));
		list($id) = $query->execute($this->db);
		return $id;
	}

	protected function update($table, array $data, array $where)
	{
		$query = DB::update($table)
			->set($data);
		$this->apply_where_to_query($query, $where);
		$count = $query->execute($this->db);
		return $count;
	}
	
	protected function delete($table, array $where)
	{
		$query = DB::delete($table);
		$this->apply_where_to_query($query, $where);
		$count = $query->execute($this->db);
		return $count;
	}
}
