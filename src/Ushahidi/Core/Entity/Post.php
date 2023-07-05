<?php
/**
 * Ushahidi Post Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\OwnableEntity;
use Ushahidi\Contracts\ParentableEntity;

/**
 * @property int|string|null $form_id The id of the form this post belongs to
 * @property string $status The status of this post
 * @property array $published_to The list of roles that can access this post
 */
interface Post extends Entity, OwnableEntity, ParentableEntity
{
    const DEFAULT_STATUS = 'draft';
    const DEFAULT_LOCAL = 'en_US';
}
