<?php

/**
 * Ushahidi Platform Search Data
 *
 * Data transfer object for dynamic search parameters.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tools;

use Ushahidi\Core\Concerns\FilterRecords;

class SearchData
{
    use FilterRecords;

    /**
     * @var array
     */
    protected $sorting = [
        'orderby',
        'order',
        'limit',
        'offset',
    ];

    /**
     * Stores the given filters for later access.
     *
     * @param array $filters
     */
    public function __construct(array $filters = null)
    {
        if ($filters) {
            $this->setFilters($filters);
        }
    }

    /**
     * Access search filters as if they are object properties.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getFilter($key);
    }

    /**
     * Set search filters as if they are object properties.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    public function __set($key, $value)
    {
        return $this->setFilter($key, $value);
    }

    /**
     * Check if search filter exists
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->getFilter($key) !== null;
    }

    /**
     * Change the filters used for sorting.
     *
     * @param  array $sorting
     * @return $this
     */
    public function setSortingKeys(array $sorting)
    {
        $this->sorting = $sorting;
        return $this;
    }

    /**
     * Get an array of the sorting filters, with their values.
     *
     * @return array [orderby, order, limit, offset]
     */
    public function getSorting($force = false)
    {
        return $this->getFilters($this->sorting, $force);
    }
}
