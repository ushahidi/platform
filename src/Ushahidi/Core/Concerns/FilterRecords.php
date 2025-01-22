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

namespace Ushahidi\Core\Concerns;

trait FilterRecords
{
    protected $filters = [];

    /**
     * Set parameters that can be used to identify **multiple** records:
     *
     *     $this->setFilters([
     *         'role'    => 'admin',
     *         'orderby' => 'username',
     *     ]);
     *
     * @param  array $filters
     * @return $this
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Set a parameters that can be used to identify **multiple** records
     *
     *     $this->setFilter('role', 'admin');
     *
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     */
    public function setFilter($name, $value)
    {
        $this->filters[$name] = $value;
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
     * @param  array $allowed  allowed parameters
     * @param  array $force    force all parameters to be defined
     * @return array
     */
    public function getFilters(array $allowed, $force = false)
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
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getFilter($name, $default = null)
    {
        if (!isset($this->filters[$name])) {
            return $default;
        }

        $filter = $this->filters[$name];

        if (empty($filter) && !is_null($default)) {
            // An empty filter reverts to the default
            return $default;
        }

        return $filter;
    }
}
