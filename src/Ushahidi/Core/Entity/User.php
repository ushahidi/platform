<?php

/**
 * Ushahidi Platform User Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Entity;

/**
 * @property int $id
 * @property string|array $role The role(s) of the user
 */
interface User extends Entity
{
}
