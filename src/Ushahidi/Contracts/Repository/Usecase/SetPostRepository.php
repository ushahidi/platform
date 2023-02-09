<?php

/**
 * Ushahidi Platform Set Post Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Usecase;

use Ushahidi\Contracts\Entity;

interface SetPostRepository
{
    /**
     * @param  int    $post_id
     * @param  int    $set_id
     *
     * @return Entity $post
     */
    public function getPostInSet($post_id, $set_id);
}
