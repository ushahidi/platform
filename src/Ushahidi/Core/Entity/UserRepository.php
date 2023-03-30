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

use Ushahidi\Core\Contracts\Entity;
use Ushahidi\Core\Contracts\Repository\EntityCreate;
use Ushahidi\Core\Contracts\Repository\ReadRepository;
use Ushahidi\Core\Contracts\Repository\DeleteRepository;
use Ushahidi\Core\Contracts\Repository\EntityCreateMany;

interface UserRepository extends
    ReadRepository,
    EntityCreate,
    EntityCreateMany,
    DeleteRepository
{
    /**
     * @param string $email
     *
     * @return \Ushahidi\Core\Contracts\Entity
     */
    public function getByEmail($email);

    public function isUniqueEmail($email);

    public function register(Entity $entity);

    public function getResetToken(Entity $entity);

    public function isValidResetToken($token);

    public function setPassword($token, $password);

    public function deleteResetToken($token);

    public function getTotalCount(array $array);
}
