<?php

/**
 * Ushahidi Filter Records Trait
 *
 * Gives objects three methods:
 *
 * - `setFilters(Array $filters)`
 * - `getFilters(Array $allowed)`
 * - `getFilter($name, $default)`
 *
 * Used to set parameters for seaching multiple record.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait FilterRecords
{
	/**
	 * @var Array
	 */
	protected $filters = [];

	/**
	 * Set parameters that can be used to identify **multiple** records:
	 *
	 *     $this->setFilters([
	 *         'role'    => 'admin',
	 *         'orderby' => 'username',
	 *     ]);
	 *
	 * @param  Array $filters
	 * @return $this
	 */
	public function setFilters(Array $filters)
	{
		$this->filters = $filters;
		return $this;
	}

	/**
	 * Gets a specific set of allowed parameters, returning any that are defined.
	 * Optionally, can force all parameters to be defined.
	 *
	 *     $filter = $this->getFilters(['role']);
	 *     $paging = $this->getFilters(['orderby', 'limit', 'offset'], true);
	 *
	 * NOTE: Defaults cannot be provided when using this method!
	 *
	 * @param  Array $allowed
	 * @param  Array $force
	 * @return Array
	 */
	public function getFilters(Array $allowed, $force = false)
	{
		$filters = array_intersect_key($this->filters, array_flip($allowed));
		if ($force) {
			$filters += array_fill_keys($allowed, null);
		}
		return $filters;
	}

	/**
	 * Get a parameter by name. If the parameter does not exist, the default value
	 * will be returned.
	 *
	 *     // Get a parameter
	 *     $role = $this->getFilter('role');
	 *
	 *     // Get a parameter, setting a default
	 *     $role = $this->getFilter('role', 'user');
	 *
	 * @param  String $name
	 * @param  Mixed  $default
	 * @return Mixed
	 */
	protected function getFilter($name, $default = null)
	{
		if (!isset($this->filters[$name])) {
			return $default;
		}
		return $this->filters[$name];
	}
}
