<?php

/**
 * Repository for Users
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\EntityCreate;
use Ushahidi\Contracts\EntityCreateMany;
use Ushahidi\Contracts\Repository\DeleteRepository;
use Ushahidi\Contracts\Repository\ReadRepository;

interface UserRepository extends
    ReadRepository,
    EntityCreate,
    EntityCreateMany,
    DeleteRepository
{
    /**
     * @param string $email
     *
     * @return \Ushahidi\Contracts\Entity
     */
    public function getByEmail($email);

    /**
     *
     * @param [type] $token
     *
     * @return bool
     */
    public function isValidResetToken($token);

    /**
     * Undocumented function
     *
     * @param array $array
     *
     * @return int
     */
    public function getTotalCount(array $array);
}
