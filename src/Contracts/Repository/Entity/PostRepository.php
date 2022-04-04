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

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\Repository\CreateRepository;
use Ushahidi\Contracts\Repository\UpdateRepository;

interface PostRepository extends
    EntityGet,
    CreateRepository,
    UpdateRepository
{
    /**
     * @param  int $id
     * @param  int $parent_id
     * @param  string $type
     * @return \Ushahidi\Contracts\Repository\Entity\Post
     */
    public function getByIdAndParent($id, $parent_id, $type);

    /**
     * @param  string $locale
     * @param  int $parent_id
     * @param  string $type
     * @return \Ushahidi\Contracts\Repository\Entity\Post
     */
    public function getByLocale($locale, $parent_id, $type);

    /**
     * Get total number of published posts
     * @return int
     */
    public function getPublishedTotal();
}
