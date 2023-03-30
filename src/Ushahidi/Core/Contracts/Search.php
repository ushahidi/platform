<?php

/**
 * Ushahidi Platform Search Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Contracts;

interface Search
{
    /**
     * Get an array of the sorting filters, with their values.
     *
     * @param bool $force
     * @return array
     */
    public function getSorting(bool $force = false);

    /**
     * Change the filters used for sorting.
     *
     * @param  array $sorting
     * @return $this
     */
    public function setSortingKeys(array $sorting);
}
