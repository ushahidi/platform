<?php

/**
 * Repository for Media
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface MediaRepository extends
    EntityGet,
    EntityExists
{

}
