<?php

/**
 * Repository for Creating Many Entities
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository;

use Illuminate\Support\Collection;

interface EntityCreateMany
{
    /**
     * @param \Illuminate\Support\Collection<\Ushahidi\Contracts\Entity> $collection
     * @return array ids of rows created
     */
    public function createMany(Collection $collection);
}
