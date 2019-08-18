<?php

/**
 * Ushahidi Platform Admin Read Tos Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Tos;

interface ReadTosRepository
{
    /**
     * @param  int $id
     * @return Ushahidi\Core\Entity\Tag
     */
    public function get($id);
}
