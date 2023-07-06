<?php

/**
 * Ushahidi Platform Tag Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\ParentableEntity;

/**
 * @property string|array $role role(s) for this tag
 */
interface Tag extends Entity, ParentableEntity
{

}
