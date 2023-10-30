<?php

/**
 * Ushahidi Platform Admin Create Message Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Usecase;

interface CreateMessageRepository
{
    /**
     * @param  int $parent_id
     * @return boolean
     */
    public function parentExists($parent_id);
}
