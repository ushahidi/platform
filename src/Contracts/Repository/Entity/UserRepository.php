<?php

/**
 * Repository for Users
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface UserRepository extends
    EntityGet,
    EntityExists
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
