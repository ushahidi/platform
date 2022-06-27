<?php

/**
 * Repository for Set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface SetRepository extends
    EntityGet,
    EntityExists
{
    /**
     * @param  Int $set_id
     * @param  Int $post_id
     */
    public function deleteSetPost($set_id, $post_id);

    /**
     * @param Int $set_id
     * @param Int $post_id
     * @return Boolean
     */
    public function setPostExists($set_id, $post_id);

    /**
     * @param Int $set_id
     * @param Int $post_id
     */
    public function addPostToSet($set_id, $post_id);
}
