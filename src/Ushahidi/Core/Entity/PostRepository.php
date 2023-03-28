<?php

/**
 * Repository for Posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\EntityCreate;
use Ushahidi\Contracts\Repository\EntityCreateMany;
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
     * @return \Ushahidi\Core\Entity\Post
     */
    public function getByIdAndParent($id, $parent_id, $type);

    /**
     * @param  string $locale
     * @param  int $parent_id
     * @param  string $type
     * @return \Ushahidi\Core\Entity\Post
     */
    public function getByLocale($locale, $parent_id, $type);

        /**
     * @param  string $slug
     *
     * @return boolean
     */
    public function isSlugAvailable($slug);

    /**
     * @param  string $locale
     * @param  int    $parent_id
     * @param  string $type
     *
     * @return boolean
     */
    public function doesTranslationExist($locale, $parent_id, $type);

    /**
     * Checking if a post requires approval via the form it belongs too
     *
     * @param int|null $formId
     * @return boolean
     */
    public function doesPostRequireApproval($formId);

    /**
     * @param  int    $post_id
     * @param  int    $set_id
     *
     * @return \Ushahidi\Core\Entity\Post
     */
    public function getPostInSet($post_id, $set_id);


    public function getTotal();

    /**
     * Get total number of published posts
     * @return int
     */
    public function getPublishedTotal();
}
