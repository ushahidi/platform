<?php

/**
 * Ushahidi Platform User Registration Repository
 *
 * Extra repository methods for checking if user details are unique
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Usecase;

use Ushahidi\Contracts\Entity;

interface UserRegisterRepository
{
    public function isUniqueEmail($email);

    public function register(Entity $entity);
}
