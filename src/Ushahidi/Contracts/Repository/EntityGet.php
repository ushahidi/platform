<?php

/**
 * Repository for Gettable Entities
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository;

interface EntityGet
{
    /**
     * Finds an entity with ID.
     *
     * @param  int|string $id
     * @param  array $options
     *
     * @return \Ushahidi\Contracts\Entity
     */
    public function get($id);

    /**
     * Converts an array of data into an entity object.
     *
     * @param  array $data
     * @return \Ushahidi\Contracts\Entity
     */
    public function getEntity(array $data = null);
}
