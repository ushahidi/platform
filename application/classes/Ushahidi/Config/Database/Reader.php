<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Database reader for the kohana config system
 *
 * @package    Kohana/Database
 * @category   Configuration
 * @author     Kohana Team
 * @copyright  (c) 2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Ushahidi_Config_Database_Reader implements Kohana_Config_Reader
{
	protected $_db_instance;

	protected $_table_name  = 'config';

	public static $groups  = FALSE;

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

		if (isset($config['groups']))
		{
			self::$groups = $config['groups'];
		}
	}

	/**
	 * Get allowed groups
	 * @return mixed Array of groups or FALSE
	 */
	public static function groups()
	{
		return self::$groups;
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

		if (self::$groups AND ! in_array($group, self::$groups))
			return FALSE;

		$query = DB::select('config_key', 'config_value')
			->from($this->_table_name)
			->where('group_name', '=', $group)
			->execute($this->_db_instance);

		return count($query) ? array_map('json_decode', $query->as_array('config_key', 'config_value')) : FALSE;
	}
}
