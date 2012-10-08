<?php defined('SYSPATH') or die('No direct script access.');
/**
 * [Kohana Cache](api/Kohana_Cache) APC driver. Provides an opcode based
 * driver for the Kohana Cache library.
 *
 * ### Configuration example
 *
 * Below is an example of an _apc_ server configuration.
 *
 *     return array(
 *          'apc' => array(                          // Driver group
 *                  'driver'         => 'apc',         // using APC driver
 *           ),
 *     )
 *
 * In cases where only one cache group is required, if the group is named `default` there is
 * no need to pass the group name when instantiating a cache instance.
 *
 * #### General cache group configuration settings
 *
 * Below are the settings available to all types of cache driver.
 *
 * Name           | Required | Description
 * -------------- | -------- | ---------------------------------------------------------------
 * driver         | __YES__  | (_string_) The driver type to use
 *
 * ### System requirements
 *
 * *  Kohana 3.0.x
 * *  PHP 5.2.4 or greater
 * *  APC PHP extension
 *
 * @package    Kohana/Cache
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Cache_Apc extends Cache implements Cache_Arithmetic {

	/**
	 * Check for existence of the APC extension This method cannot be invoked externally. The driver must
	 * be instantiated using the `Cache::instance()` method.
	 *
	 * @param  array  $config  configuration
	 * @throws Cache_Exception
	 */
	protected function __construct(array $config)
	{
		if ( ! extension_loaded('apc'))
		{
			throw new Cache_Exception('PHP APC extension is not available.');
		}

		parent::__construct($config);
	}

	/**
	 * Retrieve a cached value entry by id.
	 *
	 *     // Retrieve cache entry from apc group
	 *     $data = Cache::instance('apc')->get('foo');
	 *
	 *     // Retrieve cache entry from apc group and return 'bar' if miss
	 *     $data = Cache::instance('apc')->get('foo', 'bar');
	 *
	 * @param   string  $id       id of cache to entry
	 * @param   string  $default  default value to return if cache miss
	 * @return  mixed
	 * @throws  Cache_Exception
	 */
	public function get($id, $default = NULL)
	{
		$data = apc_fetch($this->_sanitize_id($id), $success);

		return $success ? $data : $default;
	}

	/**
	 * Set a value to cache with id and lifetime
	 *
	 *     $data = 'bar';
	 *
	 *     // Set 'bar' to 'foo' in apc group, using default expiry
	 *     Cache::instance('apc')->set('foo', $data);
	 *
	 *     // Set 'bar' to 'foo' in apc group for 30 seconds
	 *     Cache::instance('apc')->set('foo', $data, 30);
	 *
	 * @param   string   $id        id of cache entry
	 * @param   string   $data      data to set to cache
	 * @param   integer  $lifetime  lifetime in seconds
	 * @return  boolean
	 */
	public function set($id, $data, $lifetime = NULL)
	{
		if ($lifetime === NULL)
		{
			$lifetime = Arr::get($this->_config, 'default_expire', Cache::DEFAULT_EXPIRE);
		}

		return apc_store($this->_sanitize_id($id), $data, $lifetime);
	}

	/**
	 * Delete a cache entry based on id
	 *
	 *     // Delete 'foo' entry from the apc group
	 *     Cache::instance('apc')->delete('foo');
	 *
	 * @param   string  $id  id to remove from cache
	 * @return  boolean
	 */
	public function delete($id)
	{
		return apc_delete($this->_sanitize_id($id));
	}

	/**
	 * Delete all cache entries.
	 *
	 * Beware of using this method when
	 * using shared memory cache systems, as it will wipe every
	 * entry within the system for all clients.
	 *
	 *     // Delete all cache entries in the apc group
	 *     Cache::instance('apc')->delete_all();
	 *
	 * @return  boolean
	 */
	public function delete_all()
	{
		return apc_clear_cache('user');
	}

	/**
	 * Increments a given value by the step value supplied.
	 * Useful for shared counters and other persistent integer based
	 * tracking.
	 *
	 * @param   string    id of cache entry to increment
	 * @param   int       step value to increment by
	 * @return  integer
	 * @return  boolean
	 */
	public function increment($id, $step = 1)
	{
		return apc_inc($id, $step);
	}

	/**
	 * Decrements a given value by the step value supplied.
	 * Useful for shared counters and other persistent integer based
	 * tracking.
	 *
	 * @param   string    id of cache entry to decrement
	 * @param   int       step value to decrement by
	 * @return  integer
	 * @return  boolean
	 */
	public function decrement($id, $step = 1)
	{
		return apc_dec($id, $step);
	}

} // End Kohana_Cache_Apc
