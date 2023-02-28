<?php

/**
 * Repository for Tags
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\EntityCreate;
use Ushahidi\Contracts\EntityCreateMany;
use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface TagRepository extends
    EntityGet,
    EntityCreate,
    EntityCreateMany,
    EntityExists
{
    public function doesTagExist($value);
}
