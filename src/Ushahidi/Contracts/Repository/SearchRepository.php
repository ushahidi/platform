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
use Ushahidi\Core\Tool\SearchData;

interface SearchRepository
{
    /**
     * Converts an array of entity data into an object.
     * @param array $data
     * @return \Ushahidi\Contracts\Entity
     */
    public function getEntity(array $data = null);

    /**
     * Get fields that can be used for searches.
     * @return array
     */
    public function getSearchFields();

    /**
     * @param \Ushahidi\Contracts\Search $search
     *
     * @return $this
     */
    public function setSearchParams(Search $search);

    /**
     * @return [Ushahidi\Core\Entity, ...]
     */
    public function getSearchResults();

    /**
     * @return Integer
     */
    public function getSearchTotal();
}
