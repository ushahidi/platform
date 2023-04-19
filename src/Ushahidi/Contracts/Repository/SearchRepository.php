<?php

/**
 * Ushahidi Search Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository;

use Ushahidi\Contracts\Search;

interface SearchRepository extends EntityGet
{
    /**
     * Get fields that can be used for searches.
     *
     * @return array
     */
    public function getSearchFields();

    /**
     * Set the search parameters.
     *
     * @param \Ushahidi\Contracts\Search $search
     * @return $this
     */
    public function setSearchParams(Search $search);

    /**
     * Get the results for the last search.
     *
     * @return \Ushahidi\Contracts\Entity[]
     */
    public function getSearchResults();

    /**
     * Get the total number of results for the last search.
     *
     * @return integer
     */
    public function getSearchTotal();
}
