<?php

/**
 * Repository for Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\ReadRepository;
use Ushahidi\Contracts\Repository\DeleteRepository;
use Ushahidi\Contracts\Repository\UpdateRepository;

interface ConfigRepository extends ReadRepository, UpdateRepository, DeleteRepository
{
    /**
     * @return array
     */
    public function groups();

    /**
     * @param  array $groups
     * @return array
     */
    public function all(array $groups = null);
}
