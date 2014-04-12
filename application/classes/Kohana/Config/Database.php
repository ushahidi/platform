<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Backwards compatibility extension for the database writer.
 *
 * @package    Kohana/Database
 * @category   Configuration
 * @author     Kohana Team
 * @copyright  (c) 2014 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Config_Database implements Kohana_Config_Writer, Kohana_Config_Reader
{

	protected $_db_instance;

	protected $_table_name  = 'config';

	protected $_loaded_keys = array();

	/**
	 * Constructs the database reader object
	 *
	 * @param array Configuration for the reader
	 */
	public function __construct(array $config = NULL)
	{
		if (isset($config['instance']))
		{
			$this->_db_instance = $config['instance'];
		}
		elseif ($this->_db_instance === NULL)
		{
			$this->_db_instance = Database::$default;
		}

		if (isset($config['table_name']))
		{
			$this->_table_name = $config['table_name'];
		}
	}

	/**
	 * Tries to load the specificed configuration group
	 *
	 * Returns FALSE if group does not exist or an array if it does
	 *
	 * @param  string $group Configuration group
	 * @return boolean|array
	 */
	public function load($group)
	{
		/**
		 * Prevents the catch-22 scenario where the database config reader attempts to load the 
		 * database connections details from the database.
		 *
		 * @link http://dev.kohanaframework.org/issues/4316
		 */
		if ($group === 'database')
			return FALSE;

		$query = DB::select('config_key', 'config_value')
			->from($this->_table_name)
			->where('group_name', '=', $group)
			->execute($this->_db_instance);

		if (count($query))
		{
			$config = $query->as_array('config_key', 'config_value');
			$config = array_map(array($this, '_decode'), $config);
		}

		if (empty($config))
		{
			return FALSE;
		}

		return $this->_loaded_keys[$group] = $config;
	}

	/**
	 * Writes the passed config for $group
	 *
	 * Returns chainable instance on success or throws 
	 * Kohana_Config_Exception on failure
	 *
	 * @param string      $group  The config group
	 * @param string      $key    The config key to write to
	 * @param array       $config The configuration to write
	 * @return boolean
	 */
	public function write($group, $key, $config)
	{
		$config = $this->_encode($config);

		// Check to see if we've loaded the config from the table already
		if (isset($this->_loaded_keys[$group][$key]))
		{
			$this->_update($group, $key, $config);
		}
		else
		{
			// Attempt to run an insert query
			// This may fail if the config key already exists in the table
			// and we don't know about it
			try
			{
				$this->_insert($group, $key, $config);
			}
			catch (Database_Exception $e)
			{
				// Attempt to run an update instead
				$this->_update($group, $key, $config);
			}
		}

		return TRUE;
	}

	/**
	 * Encode a configuration value for storage.
	 * 
	 * The default is to use PHP serialize()
	 *
	 * @param  mixed  $value  raw config value
	 * @return string
	 */
	protected function _encode($value)
	{
		return serialize($value);
	}

	/**
	 * Decode a configuration value from storage.
	 * 
	 * The default is to use PHP unserialize()
	 *
	 * @param  string  $value  encoded config value
	 * @return mixed
	 */
	protected function _decode($value)
	{
		return unserialize($value);
	}

	/**
	 * Insert the config values into the table
	 *
	 * @param string      $group  The config group
	 * @param string      $key    The config key to write to
	 * @param array       $config The serialized configuration to write
	 * @return boolean
	 */
	protected function _insert($group, $key, $config)
	{
		DB::insert($this->_table_name, array('group_name', 'config_key', 'config_value'))
			->values(array($group, $key, $config))
			->execute($this->_db_instance);

		return $this;
	}

	/**
	 * Update the config values in the table
	 *
	 * @param string      $group  The config group
	 * @param string      $key    The config key to write to
	 * @param array       $config The serialized configuration to write
	 * @return boolean
	 */
	protected function _update($group, $key, $config)
	{
		DB::update($this->_table_name)
			->set(array('config_value' => $config))
			->where('group_name', '=', $group)
			->where('config_key', '=', $key)
			->execute($this->_db_instance);

		return $this;
	}

}
