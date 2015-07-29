<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Repository_Post_ValueFactory
{
	// a map of value type to factory closures
	protected $map = array();

	public function __construct($map = array())
	{
		$this->map = $map;
	}

	/**
	 * Get repository for post value `$type`
	 * @param  string $type
	 * @return Ushahidi\Core\Entity\PostValueRepository
	 */
	public function getRepo($type)
	{
		return $this->map[$type]();
	}

	/**
	 * Get an array of post value types (based on injected map)
	 * @return array
	 */
	public function getTypes()
	{
		return array_keys($this->map);
	}

	public function proxy(Array $include_types = [])
	{
		return new Ushahidi_Repository_Post_ValueProxy($this, $include_types);
	}

	public function each($callback, Array $include_types = [])
	{
		$map = $this->map;
		if ($include_types)
		{
			$map = array_intersect_key($this->map, array_fill_keys($include_types, TRUE));
		}

		foreach ($map as $type => $class)
		{
			$repo = $this->getRepo($type);
			$callback($repo);
		}
	}
}
