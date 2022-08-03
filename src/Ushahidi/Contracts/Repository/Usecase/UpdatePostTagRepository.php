<?php

/**
 * Ushahidi Platform Update Post Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Usecase;

use Ushahidi\Contracts\Entity;

interface UpdatePostTagRepository
{
    /**
     * @param int $id
     *
     * @return Entity
     */
    public function get($id);

    /**
     * @param string $tag
     *
     * @return Entity
     */
    public function getByTag($tag);

    /**
     * @param string|Entity $tag_or_id
     *
     * @return boolean
     */
    public function doesTagExist($tag_or_id);
}
