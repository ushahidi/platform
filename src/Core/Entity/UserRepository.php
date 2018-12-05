<?php

/**
 * Repository for Users
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;
use Ushahidi\Core\Entity\Repository\EntityCreate;
use Ushahidi\Core\Entity\Repository\EntityCreateMany;

interface UserRepository extends
    EntityGet,
    EntityExists,
    EntityCreate,
    EntityCreateMany
{
    /**
     * @param string $email
     * @return \Ushahidi\Core\Entity\User
     */
    public function getByEmail($email);
}
