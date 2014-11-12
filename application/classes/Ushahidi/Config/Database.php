<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Ushahidi configuration loader
 *
 * Uses database config, but only allows specific config groups to be used.
 * Also applies JSON enconding, rather than using native serialization.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Ushahidi_Config_Database extends Config_Database
{

	protected static $groups;

	/**
	 * Get allowed groups
	 * @return array
	 */
	public static function groups()
	{
		return static::$groups ?: array();
	}

	public function __construct(array $config = NULL)
	{
		parent::__construct($config);

		if (isset($config['groups']))
		{
			static::$groups = $config['groups'];
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
		if (! in_array($group, static::groups()))
		{
			return FALSE;
		}

		return parent::load($group);
	}

	/**
	 * Encode a configuration value for storage.
	 * 
	 * Uses json_encode()
	 *
	 * @param  mixed  $value  raw config value
	 * @return string
	 */
	protected function _encode($value)
	{
		return json_encode($value);
	}

	/**
	 * Decode a configuration value from storage.
	 * 
	 * Uses json_decode()
	 *
	 * @param  string  $value  encoded config value
	 * @return mixed
	 */
	protected function _decode($value)
	{
		return json_decode($value, true);
	}

}
