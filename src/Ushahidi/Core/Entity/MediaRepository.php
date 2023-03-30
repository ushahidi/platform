<?php

/**
 * Repository for Media
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Contracts\Repository\EntityGet;
use Ushahidi\Core\Contracts\Repository\EntityExists;
use Ushahidi\Core\Contracts\Repository\SearchRepository;

interface MediaRepository extends
    EntityGet,
    EntityExists,
    SearchRepository
{

}
