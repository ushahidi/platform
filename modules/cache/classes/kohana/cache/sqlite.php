<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Kohana Cache Sqlite Driver
 *
 * Requires SQLite3 and PDO
 *
 * @package    Kohana/Cache
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Cache_Sqlite extends Cache implements Cache_Tagging, Cache_GarbageCollect {

	/**
	 * Database resource
	 *
	 * @var  PDO
	 */
	protected $_db;

	/**
	 * Sets up the PDO SQLite table and
	 * initialises the PDO connection
	 *
	 * @param  array  $config  configuration
	 * @throws  Cache_Exception
	 */
	protected function __construct(array $config)
	{
		parent::__construct($config);

		$database = Arr::get($this->_config, 'database', NULL);

		if ($database === NULL)
		{
			throw new Cache_Exception('Database path not available in Kohana Cache configuration');
		}

		// Load new Sqlite DB
		$this->_db = new PDO('sqlite:'.$database);

		// Test for existing DB
		$result = $this->_db->query("SELECT * FROM sqlite_master WHERE name = 'caches' AND type = 'table'")->fetchAll();

		// If there is no table, create a new one
		if (0 == count($result))
		{
			$database_schema = Arr::get($this->_config, 'schema', NULL);

			if ($database_schema === NULL)
			{
				throw new Cache_Exception('Database schema not found in Kohana Cache configuration');
			}

			try
			{
				// Create the caches table
				$this->_db->query(Arr::get($this->_config, 'schema', NULL));
			}
			catch (PDOException $e)
			{
				throw new Cache_Exception('Failed to create new SQLite caches table with the following error : :error', array(':error' => $e->getMessage()));
			}
		}
	}

	/**
	 * Retrieve a value based on an id
	 *
	 * @param   string  $id       id
	 * @param   string  $default  default [Optional] Default value to return if id not found
	 * @return  mixed
	 * @throws  Cache_Exception
	 */
	public function get($id, $default = NULL)
	{
		// Prepare statement
		$statement = $this->_db->prepare('SELECT id, expiration, cache FROM caches WHERE id = :id LIMIT 0, 1');

		// Try and load the cache based on id
		try
		{
			$statement->execute(array(':id' => $this->_sanitize_id($id)));
		}
		catch (PDOException $e)
		{
			throw new Cache_Exception('There was a problem querying the local SQLite3 cache. :error', array(':error' => $e->getMessage()));
		}

		if ( ! $result = $statement->fetch(PDO::FETCH_OBJ))
		{
			return $default;
		}

		// If the cache has expired
		if ($result->expiration != 0 and $result->expiration <= time())
		{
			// Delete it and return default value
			$this->delete($id);
			return $default;
		}
		// Otherwise return cached object
		else
		{
			// Disable notices for unserializing
			$ER = error_reporting(~E_NOTICE);

			// Return the valid cache data
			$data = unserialize($result->cache);

			// Turn notices back on
			error_reporting($ER);

			// Return the resulting data
			return $data;
		}
	}

	/**
	 * Set a value based on an id. Optionally add tags.
	 *
	 * @param   string   $id        id
	 * @param   mixed    $data      data
	 * @param   integer  $lifetime  lifetime [Optional]
	 * @return  boolean
	 */
	public function set($id, $data, $lifetime = NULL)
	{
		return (bool) $this->set_with_tags($id, $data, $lifetime);
	}

	/**
	 * Delete a cache entry based on id
	 *
	 * @param   string  $id  id
	 * @return  boolean
	 * @throws  Cache_Exception
	 */
	public function delete($id)
	{
		// Prepare statement
		$statement = $this->_db->prepare('DELETE FROM caches WHERE id = :id');

		// Remove the entry
		try
		{
			$statement->execute(array(':id' => $this->_sanitize_id($id)));
		}
		catch (PDOException $e)
		{
			throw new Cache_Exception('There was a problem querying the local SQLite3 cache. :error', array(':error' => $e->getMessage()));
		}

		return (bool) $statement->rowCount();
	}

	/**
	 * Delete all cache entries
	 *
	 * @return  boolean
	 */
	public function delete_all()
	{
		// Prepare statement
		$statement = $this->_db->prepare('DELETE FROM caches');

		// Remove the entry
		try
		{
			$statement->execute();
		}
		catch (PDOException $e)
		{
			throw new Cache_Exception('There was a problem querying the local SQLite3 cache. :error', array(':error' => $e->getMessage()));
		}

		return (bool) $statement->rowCount();
	}

	/**
	 * Set a value based on an id. Optionally add tags.
	 *
	 * @param   string   $id        id
	 * @param   mixed    $data      data
	 * @param   integer  $lifetime  lifetime [Optional]
	 * @param   array    $tags      tags [Optional]
	 * @return  boolean
	 * @throws  Cache_Exception
	 */
	public function set_with_tags($id, $data, $lifetime = NULL, array $tags = NULL)
	{
		// Serialize the data
		$data = serialize($data);

		// Normalise tags
		$tags = (NULL === $tags) ? NULL : ('<'.implode('>,<', $tags).'>');

		// Setup lifetime
		if ($lifetime === NULL)
		{
			$lifetime = (0 === Arr::get($this->_config, 'default_expire', NULL)) ? 0 : (Arr::get($this->_config, 'default_expire', Cache::DEFAULT_EXPIRE) + time());
		}
		else
		{
			$lifetime = (0 === $lifetime) ? 0 : ($lifetime + time());
		}

		// Prepare statement
		// $this->exists() may throw Cache_Exception, no need to catch/rethrow
		$statement = $this->exists($id) ? $this->_db->prepare('UPDATE caches SET expiration = :expiration, cache = :cache, tags = :tags WHERE id = :id') : $this->_db->prepare('INSERT INTO caches (id, cache, expiration, tags) VALUES (:id, :cache, :expiration, :tags)');

		// Try to insert
		try
		{
			$statement->execute(array(':id' => $this->_sanitize_id($id), ':cache' => $data, ':expiration' => $lifetime, ':tags' => $tags));
		}
		catch (PDOException $e)
		{
			throw new Cache_Exception('There was a problem querying the local SQLite3 cache. :error', array(':error' => $e->getMessage()));
		}

		return (bool) $statement->rowCount();
	}

	/**
	 * Delete cache entries based on a tag
	 *
	 * @param   string  $tag  tag
	 * @return  boolean
	 * @throws  Cache_Exception
	 */
	public function delete_tag($tag)
	{
		// Prepare the statement
		$statement = $this->_db->prepare('DELETE FROM caches WHERE tags LIKE :tag');

		// Try to delete
		try
		{
			$statement->execute(array(':tag' => "%<{$tag}>%"));
		}
		catch (PDOException $e)
		{
			throw new Cache_Exception('There was a problem querying the local SQLite3 cache. :error', array(':error' => $e->getMessage()));
		}

		return (bool) $statement->rowCount();
	}

	/**
	 * Find cache entries based on a tag
	 *
	 * @param   string  $tag  tag
	 * @return  array
	 * @throws  Cache_Exception
	 */
	public function find($tag)
	{
		// Prepare the statement
		$statement = $this->_db->prepare('SELECT id, cache FROM caches WHERE tags LIKE :tag');

		// Try to find
		try
		{
			if ( ! $statement->execute(array(':tag' => "%<{$tag}>%")))
			{
				return array();
			}
		}
		catch (PDOException $e)
		{
			throw new Cache_Exception('There was a problem querying the local SQLite3 cache. :error', array(':error' => $e->getMessage()));
		}

		$result = array();

		while ($row = $statement->fetchObject())
		{
			// Disable notices for unserializing
			$ER = error_reporting(~E_NOTICE);

			$result[$row->id] = unserialize($row->cache);

			// Turn notices back on
			error_reporting($ER);
		}

		return $result;
	}

	/**
	 * Garbage collection method that cleans any expired
	 * cache entries from the cache.
	 *
	 * @return  void
	 */
	public function garbage_collect()
	{
		// Create the sequel statement
		$statement = $this->_db->prepare('DELETE FROM caches WHERE expiration < :expiration');

		try
		{
			$statement->execute(array(':expiration' => time()));
		}
		catch (PDOException $e)
		{
			throw new Cache_Exception('There was a problem querying the local SQLite3 cache. :error', array(':error' => $e->getMessage()));
		}
	}

	/**
	 * Tests whether an id exists or not
	 *
	 * @param   string  $id  id
	 * @return  boolean
	 * @throws  Cache_Exception
	 */
	protected function exists($id)
	{
		$statement = $this->_db->prepare('SELECT id FROM caches WHERE id = :id');
		try
		{
			$statement->execute(array(':id' => $this->_sanitize_id($id)));
		}
		catch (PDOExeption $e)
		{
			throw new Cache_Exception('There was a problem querying the local SQLite3 cache. :error', array(':error' => $e->getMessage()));
		}

		return (bool) $statement->fetchAll();
	}
}
