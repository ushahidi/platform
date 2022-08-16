<?php

/**
 * Repository for Posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityCreate;
use Ushahidi\Contracts\EntityCreateMany;
use Ushahidi\Contracts\Repository\CreateRepository;
use Ushahidi\Contracts\Repository\SearchRepository;
use Ushahidi\Contracts\Repository\UpdateRepository;

interface PostRepository extends
    EntityCreate,
    EntityCreateMany,
    CreateRepository,
    UpdateRepository,
    SearchRepository
{
    /**
     * @param  int $id
     * @param  int $parent_id
     * @param  string $type
     * @return \Ushahidi\Contracts\Entity
     */
    public function getByIdAndParent($id, $parent_id, $type);

    /**
     * @param  string $locale
     * @param  int $parent_id
     * @param  string $type
     * @return \Ushahidi\Contracts\Entity
     */
    public function getByLocale($locale, $parent_id, $type);

    public function getTotal();
}
