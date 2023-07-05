<?php

/**
 * Ushahidi Platform Ownable Entity Contract
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

/**
 * @property int|string $user_id the id of the owner entity
 */
interface OwnableEntity extends Entity
{

}
