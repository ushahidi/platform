<?php

/**
 * Ushahidi Platform User Password Reset Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\UserRepository;

interface ResetPasswordRepository extends UserRepository
{
	public function getResetToken(Entity $entity);

	public function isValidResetToken($token);

	public function setPassword($token, $password);

	public function deleteResetToken($token);
}
